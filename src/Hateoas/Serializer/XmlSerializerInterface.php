<?php

namespace Hateoas\Serializer;

use Hateoas\Model\Embed;
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
     */
    public function serializeLinks(array $links, XmlSerializationVisitor $visitor);

    /**
     * @param Embed[]                 $embeds
     * @param XmlSerializationVisitor $visitor
     * @param SerializationContext    $context
     */
    public function serializeEmbedded(array $embeds, XmlSerializationVisitor $visitor, SerializationContext $context);
}
