<?php

namespace Hateoas\Serializer;

use Metadata\MetadataFactoryInterface;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
interface JMSSerializerMetadataAwareInterface
{
    public function setMetadataFactory(MetadataFactoryInterface $metadataFactory);
}
