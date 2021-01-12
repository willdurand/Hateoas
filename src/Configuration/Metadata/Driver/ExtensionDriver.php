<?php

declare(strict_types=1);

namespace Hateoas\Configuration\Metadata\Driver;

use Hateoas\Configuration\Metadata\ClassMetadata;
use Hateoas\Configuration\Metadata\ConfigurationExtensionInterface;
use Metadata\ClassMetadata as JMSClassMetadata;
use Metadata\Driver\DriverInterface;

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

    public function loadMetadataForClass(\ReflectionClass $class): ?JMSClassMetadata
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

        if ($newMetadata && count($metadata->getRelations()) < 1) {
            $metadata = null;
        }

        return $metadata;
    }

    public function registerExtension(ConfigurationExtensionInterface $extension): void
    {
        $this->extensions[] = $extension;
    }
}
