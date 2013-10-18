<?php

namespace Hateoas\Serializer;

use Hateoas\Model\Embed;
use Hateoas\Model\Link;
use JMS\Serializer\JsonSerializationVisitor;
use JMS\Serializer\SerializationContext;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
interface JsonSerializerInterface
{
    /**
     * @param Link[]                   $links
     * @param JsonSerializationVisitor $visitor
     */
    public function serializeLinks(array $links, JsonSerializationVisitor $visitor);

    /**
     * @param Embed[]                  $embeds
     * @param JsonSerializationVisitor $visitor
     * @param SerializationContext     $context
     */
    public function serializeEmbedded(array $embeds, JsonSerializationVisitor $visitor, SerializationContext $context);
}
