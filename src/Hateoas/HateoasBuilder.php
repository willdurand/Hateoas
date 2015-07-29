<?php

namespace Hateoas;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\FileCacheReader;
use Hateoas\Configuration\Metadata\ConfigurationExtensionInterface;
use Hateoas\Configuration\Metadata\Driver\AnnotationDriver;
use Hateoas\Configuration\Metadata\Driver\ExtensionDriver;
use Hateoas\Configuration\Metadata\Driver\YamlDriver;
use Hateoas\Configuration\Metadata\Driver\XmlDriver;
use Hateoas\Configuration\Provider\Resolver\MethodResolver;
use Hateoas\Configuration\Provider\Resolver\ChainResolver;
use Hateoas\Configuration\Provider\RelationProvider;
use Hateoas\Configuration\Provider\Resolver\RelationProviderResolverInterface;
use Hateoas\Configuration\Provider\Resolver\StaticMethodResolver;
use Hateoas\Configuration\RelationsRepository;
use Hateoas\Expression\ExpressionEvaluator;
use Hateoas\Expression\ExpressionFunctionInterface;
use Hateoas\Expression\LinkExpressionFunction;
use Hateoas\Factory\EmbeddedsFactory;
use Hateoas\Factory\LinkFactory;
use Hateoas\Factory\LinksFactory;
use Hateoas\Helper\LinkHelper;
use Hateoas\UrlGenerator\UrlGeneratorInterface;
use Hateoas\UrlGenerator\UrlGeneratorRegistry;
use Hateoas\Serializer\EventSubscriber\JsonEventSubscriber;
use Hateoas\Serializer\EventSubscriber\XmlEventSubscriber;
use Hateoas\Serializer\ExclusionManager;
use Hateoas\Serializer\JsonHalSerializer;
use Hateoas\Serializer\JsonSerializerInterface;
use Hateoas\Serializer\JMSSerializerMetadataAwareInterface;
use Hateoas\Serializer\Metadata\InlineDeferrer;
use Hateoas\Serializer\XmlSerializer;
use Hateoas\Serializer\XmlSerializerInterface;
use JMS\Serializer\EventDispatcher\EventDispatcherInterface;
use JMS\Serializer\SerializerBuilder;
use Metadata\Cache\FileCache;
use Metadata\Driver\DriverChain;
use Metadata\Driver\FileLocator;
use Metadata\MetadataFactory;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 *
 * Some code (metadata things) from this class has been taken from
 * https://github.com/schmittjoh/serializer/blob/a29f1e5083654ba2c126acd94ddb2287069b0b5b/src/JMS/Serializer/SerializerBuilder.php
 */
class HateoasBuilder
{
    /**
     * @var SerializerBuilder
     */
    private $serializerBuilder;

    /**
     * @var ExpressionLanguage
     */
    private $expressionLanguage;

    /**
     * @var array
     */
    private $contextVariables = array();

    /**
     * ExpressionFunctionInterface[]
     */
    private $expressionFunctions = array();

    /**
     * @var XmlSerializerInterface
     */
    private $xmlSerializer;

    /**
     * @var JsonSerializerInterface
     */
    private $jsonSerializer;

    /**
     * @var UrlGeneratorRegistry
     */
    private $urlGeneratorRegistry;

    private $configurationExtensions = array();

    private $chainResolver;

    private $metadataDirs = array();

    private $debug = false;

    private $cacheDir;

    private $annotationReader;

    private $includeInterfaceMetadata = false;

    /**
     * @param SerializerBuilder $serializerBuilder
     *
     * @return HateoasBuilder
     */
    public static function create(SerializerBuilder $serializerBuilder = null)
    {
        return new static($serializerBuilder);
    }

    /**
     * @return Hateoas
     */
    public static function buildHateoas()
    {
        $builder = static::create();

        return $builder->build();
    }

    public function __construct(SerializerBuilder $serializerBuilder = null)
    {
        $this->serializerBuilder    = $serializerBuilder ?: SerializerBuilder::create();
        $this->urlGeneratorRegistry = new UrlGeneratorRegistry();
        $this->chainResolver        = new ChainResolver(array(
            new MethodResolver(),
            new StaticMethodResolver(),
        ));
    }

    /**
     * Build a configured Hateoas instance.
     *
     * @return Hateoas
     */
    public function build()
    {
        $metadataFactory     = $this->buildMetadataFactory();
        $relationProvider    = new RelationProvider($metadataFactory, $this->chainResolver);
        $relationsRepository = new RelationsRepository($metadataFactory, $relationProvider);
        $expressionEvaluator = new ExpressionEvaluator($this->getExpressionLanguage(), $this->contextVariables);
        $linkFactory         = new LinkFactory($expressionEvaluator, $this->urlGeneratorRegistry);
        $exclusionManager    = new ExclusionManager($expressionEvaluator);
        $linksFactory        = new LinksFactory($relationsRepository, $linkFactory, $exclusionManager);
        $embeddedsFactory    = new EmbeddedsFactory($relationsRepository, $expressionEvaluator, $exclusionManager);
        $linkHelper          = new LinkHelper($linkFactory, $relationsRepository);

        // Register Hateoas core functions
        $expressionEvaluator->registerFunction(new LinkExpressionFunction($linkHelper));

        // Register user functions
        foreach ($this->expressionFunctions as $expressionFunction) {
            $expressionEvaluator->registerFunction($expressionFunction);
        }

        if (null === $this->xmlSerializer) {
            $this->setDefaultXmlSerializer();
        }

        if (null === $this->jsonSerializer) {
            $this->setDefaultJsonSerializer();
        }

        $inlineDeferrers  = array();
        $eventSubscribers = array(
            new XmlEventSubscriber($this->xmlSerializer, $linksFactory, $embeddedsFactory),
            new JsonEventSubscriber(
                $this->jsonSerializer,
                $linksFactory,
                $embeddedsFactory,
                $inlineDeferrers[] = new InlineDeferrer(),
                $inlineDeferrers[] = new InlineDeferrer()
            ),
        );

        $this->serializerBuilder
            ->addDefaultListeners()
            ->configureListeners(function (EventDispatcherInterface $dispatcher) use ($eventSubscribers) {
                foreach ($eventSubscribers as $eventSubscriber) {
                    $dispatcher->addSubscriber($eventSubscriber);
                }
            })
        ;

        $jmsSerializer = $this->serializerBuilder->build();
        foreach (array_merge($inlineDeferrers, array($this->jsonSerializer, $this->xmlSerializer)) as $serializer) {
            if ($serializer instanceof JMSSerializerMetadataAwareInterface) {
                $serializer->setMetadataFactory($jmsSerializer->getMetadataFactory());
            }
        }

        return new Hateoas($jmsSerializer, $linkHelper);
    }

    /**
     * @param XmlSerializerInterface $xmlSerializer
     *
     * @return HateoasBuilder
     */
    public function setXmlSerializer(XmlSerializerInterface $xmlSerializer)
    {
        $this->xmlSerializer = $xmlSerializer;

        return $this;
    }

    /**
     * Set the default XML serializer (`XmlSerializer`).
     *
     * @return HateoasBuilder
     */
    public function setDefaultXmlSerializer()
    {
        return $this->setXmlSerializer(new XmlSerializer());
    }

    /**
     * @param JsonSerializerInterface $jsonSerializer
     *
     * @return HateoasBuilder
     */
    public function setJsonSerializer(JsonSerializerInterface $jsonSerializer)
    {
        $this->jsonSerializer = $jsonSerializer;

        return $this;
    }

    /**
     * Set the default JSON serializer (`JsonHalSerializer`).
     *
     * @return HateoasBuilder
     */
    public function setDefaultJsonSerializer()
    {
        return $this->setJsonSerializer(new JsonHalSerializer());
    }

    /**
     * Add a new URL generator. If you pass `null` as name, it will be the
     * default URL generator.
     *
     * @param string|null           $name
     * @param UrlGeneratorInterface $urlGenerator
     *
     * @return HateoasBuilder
     */
    public function setUrlGenerator($name, UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGeneratorRegistry->set($name, $urlGenerator);

        return $this;
    }

    /**
     * Add a new expression context variable.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return HateoasBuilder
     */
    public function setExpressionContextVariable($name, $value)
    {
        $this->contextVariables[$name] = $value;

        return $this;
    }

    /**
     * @param ExpressionLanguage $expressionLanguage
     *
     * @return HateoasBuilder
     */
    public function setExpressionLanguage(ExpressionLanguage $expressionLanguage)
    {
        $this->expressionLanguage = $expressionLanguage;

        return $this;
    }

    /**
     * @param ExpressionFunctionInterface $expressionFunction
     *
     * @return HateoasBuilder
     */
    public function registerExpressionFunction(ExpressionFunctionInterface $expressionFunction)
    {
        $this->expressionFunctions[] = $expressionFunction;

        return $this;
    }

    /**
     * Add a new relation provider resolver.
     *
     * @param RelationProviderResolverInterface $resolver
     *
     * @return HateoasBuilder
     */
    public function addRelationProviderResolver(RelationProviderResolverInterface $resolver)
    {
        $this->chainResolver->addResolver($resolver);

        return $this;
    }

    /**
     * @param ConfigurationExtensionInterface $configurationExtension
     *
     * @return HateoasBuilder
     */
    public function addConfigurationExtension(ConfigurationExtensionInterface $configurationExtension)
    {
        $this->configurationExtensions[] = $configurationExtension;

        return $this;
    }

    /**
     * @param boolean $debug
     *
     * @return HateoasBuilder
     */
    public function setDebug($debug)
    {
        $this->debug = (boolean) $debug;

        return $this;
    }

    /**
     * @param string $dir
     *
     * @return HateoasBuilder
     */
    public function setCacheDir($dir)
    {
        if (!is_dir($dir)) {
            $this->createDir($dir);
        }

        if (!is_writable($dir)) {
            throw new \InvalidArgumentException(sprintf('The cache directory "%s" is not writable.', $dir));
        }

        $this->cacheDir = $dir;

        return $this;
    }

    /**
     * @param boolean $include Whether to include the metadata from the interfaces
     *
     * @return HateoasBuilder
     */
    public function includeInterfaceMetadata($include)
    {
        $this->includeInterfaceMetadata = (boolean) $include;

        return $this;
    }

    /**
     * Set a map of namespace prefixes to directories.
     *
     * This method overrides any previously defined directories.
     *
     * @param array $namespacePrefixToDirMap
     *
     * @return HateoasBuilder
     */
    public function setMetadataDirs(array $namespacePrefixToDirMap)
    {
        foreach ($namespacePrefixToDirMap as $dir) {
            if (!is_dir($dir)) {
                throw new \InvalidArgumentException(sprintf('The directory "%s" does not exist.', $dir));
            }
        }

        $this->metadataDirs = $namespacePrefixToDirMap;

        return $this;
    }

    /**
     * Add a directory where the serializer will look for class metadata.
     *
     * The namespace prefix will make the names of the actual metadata files a bit shorter. For example, let's assume
     * that you have a directory where you only store metadata files for the ``MyApplication\Entity`` namespace.
     *
     * If you use an empty prefix, your metadata files would need to look like:
     *
     * ``my-dir/MyApplication.Entity.SomeObject.yml``
     * ``my-dir/MyApplication.Entity.OtherObject.yml``
     *
     * If you use ``MyApplication\Entity`` as prefix, your metadata files would need to look like:
     *
     * ``my-dir/SomeObject.yml``
     * ``my-dir/OtherObject.yml``
     *
     * Please keep in mind that you currently may only have one directory per namespace prefix.
     *
     * @param string $dir             The directory where metadata files are located.
     * @param string $namespacePrefix An optional prefix if you only store metadata for specific namespaces in this directory.
     *
     * @return HateoasBuilder
     */
    public function addMetadataDir($dir, $namespacePrefix = '')
    {
        if (!is_dir($dir)) {
            throw new \InvalidArgumentException(sprintf('The directory "%s" does not exist.', $dir));
        }

        if (isset($this->metadataDirs[$namespacePrefix])) {
            throw new \InvalidArgumentException(sprintf('There is already a directory configured for the namespace prefix "%s". Please use replaceMetadataDir() to override directories.', $namespacePrefix));
        }

        $this->metadataDirs[$namespacePrefix] = $dir;

        return $this;
    }

    /**
     * Add a map of namespace prefixes to directories.
     *
     * @param array $namespacePrefixToDirMap
     *
     * @return HateoasBuilder
     */
    public function addMetadataDirs(array $namespacePrefixToDirMap)
    {
        foreach ($namespacePrefixToDirMap as $prefix => $dir) {
            $this->addMetadataDir($dir, $prefix);
        }

        return $this;
    }

    /**
     * Similar to addMetadataDir(), but overrides an existing entry.
     *
     * @param string $dir
     * @param string $namespacePrefix
     *
     * @return HateoasBuilder
     */
    public function replaceMetadataDir($dir, $namespacePrefix = '')
    {
        if (!is_dir($dir)) {
            throw new \InvalidArgumentException(sprintf('The directory "%s" does not exist.', $dir));
        }

        if (!isset($this->metadataDirs[$namespacePrefix])) {
            throw new \InvalidArgumentException(sprintf('There is no directory configured for namespace prefix "%s". Please use addMetadataDir() for adding new directories.', $namespacePrefix));
        }

        $this->metadataDirs[$namespacePrefix] = $dir;

        return $this;
    }

    private function buildMetadataFactory()
    {
        $annotationReader = $this->annotationReader;

        if (null === $annotationReader) {
            $annotationReader = new AnnotationReader();

            if (null !== $this->cacheDir) {
                $this->createDir($this->cacheDir.'/annotations');
                $annotationReader = new FileCacheReader($annotationReader, $this->cacheDir.'/annotations', $this->debug);
            }
        }

        if (!empty($this->metadataDirs)) {
            $fileLocator    = new FileLocator($this->metadataDirs);
            $metadataDriver = new DriverChain(array(
                new YamlDriver($fileLocator),
                new XmlDriver($fileLocator),
                new AnnotationDriver($annotationReader),
            ));
        } else {
            $metadataDriver = new AnnotationDriver($annotationReader);
        }

        $metadataDriver  = new ExtensionDriver($metadataDriver, $this->configurationExtensions);
        $metadataFactory = new MetadataFactory($metadataDriver, null, $this->debug);
        $metadataFactory->setIncludeInterfaces($this->includeInterfaceMetadata);

        if (null !== $this->cacheDir) {
            $this->createDir($this->cacheDir.'/metadata');
            $metadataFactory->setCache(new FileCache($this->cacheDir.'/metadata'));
        }

        return $metadataFactory;
    }

    /**
     * @param string $dir
     */
    private function createDir($dir)
    {
        if (is_dir($dir)) {
            return;
        }

        if (false === @mkdir($dir, 0777, true)) {
            throw new \RuntimeException(sprintf('Could not create directory "%s".', $dir));
        }
    }

    private function getExpressionLanguage()
    {
        if (null === $this->expressionLanguage) {
            $this->expressionLanguage = new ExpressionLanguage();
        }

        return $this->expressionLanguage;
    }
}
