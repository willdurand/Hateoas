<?php

namespace Hateoas;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\FileCacheReader;
use Hateoas\Configuration\Metadata\Driver\AnnotationDriver;
use Hateoas\Configuration\Metadata\Driver\YamlDriver;
use Hateoas\Configuration\RelationsRepository;
use Hateoas\Factory\EmbeddedMapFactory;
use Hateoas\Factory\LinkFactory;
use Hateoas\Factory\LinksFactory;
use Hateoas\Factory\RouteFactoryInterface;
use Hateoas\Handler\HandlerInterface;
use Hateoas\Handler\HandlerManager;
use Hateoas\Handler\PropertyPathHandler;
use Hateoas\Serializer\EventSubscriber\JsonEventSubscriber;
use Hateoas\Serializer\EventSubscriber\XmlEventSubscriber;
use Hateoas\Serializer\Handler\JsonResourceHandler;
use Hateoas\Serializer\Handler\XmlResourceHandler;
use Hateoas\Serializer\JsonHalSerializer;
use Hateoas\Serializer\JsonSerializerInterface;
use Hateoas\Serializer\XmlSerializer;
use Hateoas\Serializer\XmlSerializerInterface;
use JMS\Serializer\EventDispatcher\EventDispatcherInterface;
use JMS\Serializer\Handler\HandlerRegistryInterface;
use JMS\Serializer\SerializerBuilder;
use Metadata\Cache\FileCache;
use Metadata\Driver\DriverChain;
use Metadata\Driver\FileLocator;
use Metadata\MetadataFactory;

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

    private $handlerSet = false;
    private $handlerManager;

    private $xmlSerializer;
    private $jsonSerializer;
    private $routeFactory;

    private $metadataDirs = array();
    private $debug = false;
    private $cacheDir;
    private $annotationReader;
    private $includeInterfaceMetadata = false;

    public static function create(SerializerBuilder $serializerBuilder = null)
    {
        return new static($serializerBuilder);
    }

    public static function buildHateoas()
    {
        $builder = static::create();

        return $builder->build();
    }

    public function __construct(SerializerBuilder $serializerBuilder = null)
    {
        $this->serializerBuilder = $serializerBuilder ?: SerializerBuilder::create();
        $this->handlerManager = new HandlerManager();
    }

    public function build()
    {
        $metadataFactory = $this->buildMetadataFactory();

        $relationsRepository = new RelationsRepository($metadataFactory);
        $linkFactory = new LinkFactory($this->handlerManager, $this->routeFactory);
        $linksFactory = new LinksFactory($relationsRepository, $linkFactory);
        $embeddedMapFactory = new EmbeddedMapFactory($relationsRepository, $this->handlerManager);

        if (null === $this->xmlSerializer) {
            $this->setDefaultXmlSerializer();
        }

        if (null === $this->jsonSerializer) {
            $this->setHalJsonSerializer();
        }

        if (!$this->handlerSet) {
            $this->setDefaultHandlers();
        }

        $eventSubscribers = array(
            new XmlEventSubscriber($this->xmlSerializer, $linksFactory, $embeddedMapFactory),
            new JsonEventSubscriber($this->jsonSerializer, $linksFactory, $embeddedMapFactory),
        );
        $handlers = array(
            new XmlResourceHandler($this->xmlSerializer),
            new JsonResourceHandler($this->jsonSerializer),
        );
        $this->serializerBuilder
            ->addDefaultListeners()
            ->configureListeners(function (EventDispatcherInterface $dispatcher) use ($eventSubscribers) {
                foreach ($eventSubscribers as $eventSubscriber) {
                    $dispatcher->addSubscriber($eventSubscriber);
                }
            })
            ->configureHandlers(function (HandlerRegistryInterface $registry) use ($handlers) {
                foreach ($handlers as $handler) {
                    $registry->registerSubscribingHandler($handler);
                }
            })
        ;

        return new Hateoas($this->serializerBuilder->build(), $relationsRepository, $this->handlerManager);
    }

    public function setXmlSerializer(XmlSerializerInterface $xmlSerializer)
    {
        $this->xmlSerializer = $xmlSerializer;

        return $this;
    }

    public function setDefaultXmlSerializer()
    {
        return $this->setXmlSerializer(new XmlSerializer());
    }

    public function setJsonSerializer(JsonSerializerInterface $jsonSerializer)
    {
        $this->jsonSerializer = $jsonSerializer;

        return $this;
    }

    public function setHalJsonSerializer()
    {
        return $this->setJsonSerializer(new JsonHalSerializer());
    }

    public function setRouteFactory(RouteFactoryInterface $routeFactory)
    {
        $this->routeFactory = $routeFactory;

        return $this;
    }

    public function setHandler($name, HandlerInterface $handler)
    {
        $this->handlerManager->setHandler($name, $handler);
        $this->handlerSet = true;

        return $this;
    }

    public function setDefaultHandlers()
    {
        $this->handlerManager->setHandler('this', new PropertyPathHandler());

        return $this;
    }

    public function setDebug($bool)
    {
        $this->debug = (boolean) $bool;

        return $this;
    }

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
     * @param Boolean $include Whether to include the metadata from the interfaces
     *
     * @return SerializerBuilder
     */
    public function includeInterfaceMetadata($include)
    {
        $this->includeInterfaceMetadata = (Boolean) $include;

        return $this;
    }

    /**
     * Sets a map of namespace prefixes to directories.
     *
     * This method overrides any previously defined directories.
     *
     * @param array<string,string> $namespacePrefixToDirMap
     *
     * @return self
     *
     * @throws \InvalidArgumentException When a directory does not exist
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
     * Adds a directory where the serializer will look for class metadata.
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
     * @return self
     *
     * @throws \InvalidArgumentException When a directory does not exist
     * @throws \InvalidArgumentException When a directory has already been registered
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
     * Adds a map of namespace prefixes to directories.
     *
     * @param array<string,string> $namespacePrefixToDirMap
     *
     * @return self
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
     * @return self
     *
     * @throws \InvalidArgumentException When a directory does not exist
     * @throws \InvalidArgumentException When no directory is configured for the ns prefix
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
            $fileLocator = new FileLocator($this->metadataDirs);
            $metadataDriver = new DriverChain(array(
                new YamlDriver($fileLocator),
                new AnnotationDriver($annotationReader),
            ));
        } else {
            $metadataDriver = new AnnotationDriver($annotationReader);
        }

        $metadataFactory = new MetadataFactory($metadataDriver, null, $this->debug);
        $metadataFactory->setIncludeInterfaces($this->includeInterfaceMetadata);

        if (null !== $this->cacheDir) {
            $this->createDir($this->cacheDir.'/metadata');
            $metadataFactory->setCache(new FileCache($this->cacheDir.'/metadata'));
        }

        return $metadataFactory;
    }

    private function createDir($dir)
    {
        if (is_dir($dir)) {
            return;
        }

        if (false === @mkdir($dir, 0777, true)) {
            throw new \RuntimeException(sprintf('Could not create directory "%s".', $dir));
        }
    }
}
