<?php

namespace Hateoas\Serializer;

use Hateoas\Model\Link;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\XmlSerializationVisitor;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
interface XmlSerializerInterface
{
    /**
     * @param Link[] $links
     * @param XmlSerializationVisitor $visitor
     * @return void
     */
    public function serializeLinks(array $links, XmlSerializationVisitor $visitor);

    /**
     * @param array<string, mixed> $embeddedMap rel => data
     * @param XmlSerializationVisitor $visitor
     * @param SerializationContext $context
     * @return mixed
     */
    public function serializeEmbedded(array $embeddedMap, XmlSerializationVisitor $visitor, SerializationContext $context);
}
