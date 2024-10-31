<?php

declare(strict_types=1);

namespace Hateoas;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\FileCacheReader;
use Hateoas\Configuration\Metadata\ConfigurationExtensionInterface;
use Hateoas\Configuration\Metadata\Driver\AnnotationDriver;
use Hateoas\Configuration\Metadata\Driver\AttributeDriver;
use Hateoas\Configuration\Metadata\Driver\ExtensionDriver;
use Hateoas\Configuration\Metadata\Driver\XmlDriver;
use Hateoas\Configuration\Metadata\Driver\YamlDriver;
use Hateoas\Configuration\Provider\ChainProvider;
use Hateoas\Configuration\Provider\ExpressionEvaluatorProvider;
use Hateoas\Configuration\Provider\FunctionProvider;
use Hateoas\Configuration\Provider\StaticMethodProvider;
use Hateoas\Expression\LinkExpressionFunction;
use Hateoas\Factory\EmbeddedsFactory;
use Hateoas\Factory\LinkFactory;
use Hateoas\Factory\LinksFactory;
use Hateoas\Helper\LinkHelper;
use Hateoas\Serializer\AddRelationsListener;
use Hateoas\Serializer\ExclusionManager;
use Hateoas\Serializer\JsonHalSerializer;
use Hateoas\Serializer\Metadata\InlineDeferrer;
use Hateoas\Serializer\SerializerInterface;
use Hateoas\Serializer\XmlSerializer;
use Hateoas\UrlGenerator\UrlGeneratorInterface;
use Hateoas\UrlGenerator\UrlGeneratorRegistry;
use JMS\Serializer\EventDispatcher\EventDispatcherInterface;
use JMS\Serializer\EventDispatcher\Events;
use JMS\Serializer\Exclusion\ExpressionLanguageExclusionStrategy;
use JMS\Serializer\Expression\ExpressionEvaluator;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\Type\Parser;
use Metadata\Cache\FileCache;
use Metadata\Driver\DriverChain;
use Metadata\Driver\FileLocator;
use Metadata\MetadataFactory;
use Metadata\MetadataFactoryInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

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
     * @var ExpressionEvaluator
     */
    private $expressionEvaluator;

    /**
     * @var array
     */
    private $contextVariables = [];

    /**
     * @var SerializerInterface
     */
    private $xmlSerializer;

    /**
     * @var SerializerInterface
     */
    private $jsonSerializer;

    /**
     * @var UrlGeneratorRegistry
     */
    private $urlGeneratorRegistry;

    /**
     * @var ConfigurationExtensionInterface[]
     */
    private $configurationExtensions = [];

    /**
     * @var ChainProvider
     */
    private $chainProvider;

    /**
     * @var string[]
     */
    private $metadataDirs = [];

    /**
     * @var bool
     */
    private $debug = false;

    /**
     * @var string
     */
    private $cacheDir;

    /**
     * @var AnnotationReader
     */
    private $annotationReader;

    /**
     * @var bool
     */
    private $includeInterfaceMetadata = false;

    public static function create(?SerializerBuilder $serializerBuilder = null): HateoasBuilder
    {
        return new static($serializerBuilder);
    }

    public static function buildHateoas(): Hateoas
    {
        $builder = static::create();

        return $builder->build();
    }

    public function __construct(?SerializerBuilder $serializerBuilder = null)
    {
        $this->serializerBuilder    = $serializerBuilder ?: SerializerBuilder::create();
        $this->urlGeneratorRegistry = new UrlGeneratorRegistry();
        $this->chainProvider        = new ChainProvider([
            new FunctionProvider(),
            new StaticMethodProvider(),
        ]);
    }

    /**
     * Build a configured Hateoas instance.
     */
    public function build(): Hateoas
    {
        $metadataFactory     = $this->buildMetadataFactory();

        $linkFactory         = new LinkFactory($this->urlGeneratorRegistry);
        $this->contextVariables['link_helper'] = $linkHelper = new LinkHelper($linkFactory, $metadataFactory);

        $expressionEvaluator =  $this->getExpressionEvaluator();
        foreach ($this->contextVariables as $name => $value) {
            $expressionEvaluator->setContextVariable($name, $value);
        }

        $this->chainProvider->addProvider(new ExpressionEvaluatorProvider($expressionEvaluator));

        $linkFactory->setExpressionEvaluator($expressionEvaluator);

        $exclusionManager    = new ExclusionManager(new ExpressionLanguageExclusionStrategy($expressionEvaluator));

        $linksFactory        = new LinksFactory($metadataFactory, $linkFactory, $exclusionManager);
        $embeddedsFactory    = new EmbeddedsFactory($metadataFactory, $expressionEvaluator, $exclusionManager);

        if (null === $this->xmlSerializer) {
            $this->setDefaultXmlSerializer();
        }

        if (null === $this->jsonSerializer) {
            $this->setDefaultJsonSerializer();
        }

        $eventListeners = [
            'xml' => new AddRelationsListener(
                $this->xmlSerializer,
                $linksFactory,
                $embeddedsFactory,
                new InlineDeferrer(),
                new InlineDeferrer()
            ),
            'json' => new AddRelationsListener(
                $this->jsonSerializer,
                $linksFactory,
                $embeddedsFactory,
                new InlineDeferrer(),
                new InlineDeferrer()
            ),
        ];

        $this->serializerBuilder
            ->addDefaultListeners()
            ->configureListeners(static function (EventDispatcherInterface $dispatcher) use ($eventListeners): void {
                foreach ($eventListeners as $format => $listener) {
                    $dispatcher->addListener(Events::POST_SERIALIZE, [$listener, 'onPostSerialize'], null, $format);
                }
            });

        $this->serializerBuilder->addMetadataDirs($this->metadataDirs);
        $this->serializerBuilder->setExpressionEvaluator($this->expressionEvaluator);

        $jmsSerializer = $this->serializerBuilder->build();

        return new Hateoas($jmsSerializer, $linkHelper);
    }

    public function setXmlSerializer(SerializerInterface $xmlSerializer): HateoasBuilder
    {
        $this->xmlSerializer = $xmlSerializer;

        return $this;
    }

    /**
     * Set the default XML serializer (`XmlSerializer`).
     */
    public function setDefaultXmlSerializer(): HateoasBuilder
    {
        return $this->setXmlSerializer(new XmlSerializer());
    }

    public function setJsonSerializer(SerializerInterface $jsonSerializer): HateoasBuilder
    {
        $this->jsonSerializer = $jsonSerializer;

        return $this;
    }

    /**
     * Set the default JSON serializer (`JsonHalSerializer`).
     */
    public function setDefaultJsonSerializer(): HateoasBuilder
    {
        return $this->setJsonSerializer(new JsonHalSerializer());
    }

    /**
     * Add a new URL generator. If you pass `null` as name, it will be the
     * default URL generator.
     */
    public function setUrlGenerator(?string $name, UrlGeneratorInterface $urlGenerator): HateoasBuilder
    {
        $this->urlGeneratorRegistry->set($name, $urlGenerator);

        return $this;
    }

    /**
     * Add a new expression context variable.
     *
     * @param mixed  $value
     */
    public function setExpressionContextVariable(string $name, $value): HateoasBuilder
    {
        $this->contextVariables[$name] = $value;

        return $this;
    }

    public function setExpressionLanguage(ExpressionLanguage $expressionLanguage): HateoasBuilder
    {
        $this->expressionLanguage = $expressionLanguage;

        return $this;
    }

    public function addConfigurationExtension(ConfigurationExtensionInterface $configurationExtension): HateoasBuilder
    {
        $this->configurationExtensions[] = $configurationExtension;

        return $this;
    }

    public function setDebug(bool $debug): HateoasBuilder
    {
        $this->debug = (bool) $debug;

        return $this;
    }

    public function setCacheDir(string $dir): HateoasBuilder
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
     * @param bool $include Whether to include the metadata from the interfaces
     */
    public function includeInterfaceMetadata(bool $include): HateoasBuilder
    {
        $this->includeInterfaceMetadata = (bool) $include;

        return $this;
    }

    /**
     * Set a map of namespace prefixes to directories.
     *
     * This method overrides any previously defined directories.
     */
    public function setMetadataDirs(array $namespacePrefixToDirMap): HateoasBuilder
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
     */
    public function addMetadataDir(string $dir, string $namespacePrefix = ''): HateoasBuilder
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
     */
    public function addMetadataDirs(array $namespacePrefixToDirMap): HateoasBuilder
    {
        foreach ($namespacePrefixToDirMap as $prefix => $dir) {
            $this->addMetadataDir($dir, $prefix);
        }

        return $this;
    }

    /**
     * Similar to addMetadataDir(), but overrides an existing entry.
     */
    public function replaceMetadataDir(string $dir, string $namespacePrefix = ''): HateoasBuilder
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

    private function buildMetadataFactory(): MetadataFactoryInterface
    {
        $expressionEvaluator =  $this->getExpressionEvaluator();

        $typeParser = new Parser();

        $annotationReader = $this->annotationReader;
        $drivers = [new AttributeDriver($expressionEvaluator, $this->chainProvider, $typeParser)];

        if (null === $annotationReader && class_exists(AnnotationReader::class)) {
            $annotationReader = new AnnotationReader();

            if (null !== $this->cacheDir) {
                $this->createDir($this->cacheDir . '/annotations');
                $annotationReader = new FileCacheReader($annotationReader, $this->cacheDir . '/annotations', $this->debug);
            }

            $drivers[] = new AnnotationDriver($annotationReader, $expressionEvaluator, $this->chainProvider, $typeParser);
        }

        if (!empty($this->metadataDirs)) {
            $fileLocator = new FileLocator($this->metadataDirs);
            $drivers[] = new YamlDriver($fileLocator, $expressionEvaluator, $this->chainProvider, $typeParser);
            $drivers[] = new XmlDriver($fileLocator, $expressionEvaluator, $this->chainProvider, $typeParser);
        }

        $metadataDriver  = new ExtensionDriver(new DriverChain($drivers), $this->configurationExtensions);
        $metadataFactory = new MetadataFactory($metadataDriver, null, $this->debug);
        $metadataFactory->setIncludeInterfaces($this->includeInterfaceMetadata);

        if (null !== $this->cacheDir) {
            $this->createDir($this->cacheDir . '/metadata');
            $metadataFactory->setCache(new FileCache($this->cacheDir . '/metadata'));
        }

        return $metadataFactory;
    }

    private function createDir(string $dir): void
    {
        if (is_dir($dir)) {
            return;
        }

        if (false === @mkdir($dir, 0777, true) && !is_dir($dir)) {
            throw new \RuntimeException(sprintf('Could not create directory "%s".', $dir));
        }
    }

    private function getExpressionLanguage(): ExpressionLanguage
    {
        if (null === $this->expressionLanguage) {
            $this->expressionLanguage = new ExpressionLanguage();
            $this->expressionLanguage->registerProvider(new LinkExpressionFunction());
        }

        return $this->expressionLanguage;
    }

    private function getExpressionEvaluator(): ExpressionEvaluator
    {
        if (null === $this->expressionEvaluator) {
            $this->expressionEvaluator = new ExpressionEvaluator($this->getExpressionLanguage(), $this->contextVariables);
        }

        return $this->expressionEvaluator;
    }
}
