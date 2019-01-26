<?php

declare(strict_types=1);

namespace Hateoas\Configuration\Metadata;

interface ConfigurationExtensionInterface
{
    public function decorate(ClassMetadataInterface $classMetadata): void;
}
