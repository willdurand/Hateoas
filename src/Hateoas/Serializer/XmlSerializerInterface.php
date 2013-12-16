<?php

namespace Hateoas\Serializer;

use Hateoas\Model\Embedded;
use Hateoas\Model\Link;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\XmlSerializationVisitor;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
interface XmlSerializerInterface
{
    /**
     * @param Link[]                  $links
     * @param XmlSerializationVisitor $visitor
     * @param SerializationContext    $context
     */
    public function serializeLinks(array $links, XmlSerializationVisitor $visitor, SerializationContext $context);

    /**
     * @param Embedded[]              $embeddeds
     * @param XmlSerializationVisitor $visitor
     * @param SerializationContext    $context
     */
    public function serializeEmbeddeds(array $embeddeds, XmlSerializationVisitor $visitor, SerializationContext $context);
}
