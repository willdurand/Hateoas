<?php

namespace Hateoas\Configuration\Metadata\Driver;

use Hateoas\Configuration\Metadata\ClassMetadata;
use Hateoas\Configuration\Metadata\ConfigurationExtensionInterface;
use Metadata\Driver\DriverInterface;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class ExtensionDriver implements DriverInterface
{
    /**
     * @var DriverInterface
     */
    private $delegate;

    /**
     * @var ConfigurationExtensionInterface[]
     */
    private $extensions;

    public function __construct(DriverInterface $delegate, array $extensions = [])
    {
        $this->delegate   = $delegate;
        $this->extensions = $extensions;
    }

    /**
     * {@inheritdoc}
     */
    public function loadMetadataForClass(\ReflectionClass $class)
    {
        $metadata    = $this->delegate->loadMetadataForClass($class);
        $newMetadata = false;

        if (empty($this->extensions)) {
            return $metadata;
        }

        if (null === $metadata) {
            $metadata    = new ClassMetadata($class->getName());
            $newMetadata = true;
        }

        foreach ($this->extensions as $extension) {
            $extension->decorate($metadata);
        }

        if ($newMetadata && count($metadata->getRelations()) < 1 && count($metadata->getRelationProviders()) < 1) {
            $metadata = null;
        }

        return $metadata;
    }

    /**
     * @param ConfigurationExtensionInterface $extension
     */
    public function registerExtension(ConfigurationExtensionInterface $extension) {
        $this->extensions[] = $extension;
    }
}
