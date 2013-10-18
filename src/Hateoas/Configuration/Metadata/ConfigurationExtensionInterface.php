<?php

namespace Hateoas\Configuration\Metadata;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
interface ConfigurationExtensionInterface
{
    public function decorate(ClassMetadataInterface $classMetadata);
}
