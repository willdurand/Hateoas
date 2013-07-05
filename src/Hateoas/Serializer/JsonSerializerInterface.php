<?php

namespace Hateoas\Serializer;

use Hateoas\Model\Link;
use JMS\Serializer\JsonSerializationVisitor;
use JMS\Serializer\SerializationContext;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
interface JsonSerializerInterface
{
    /**
     * @param Link[] $links
     * @param JsonSerializationVisitor $visitor
     * @return void
     */
    public function serializeLinks(array $links, JsonSerializationVisitor $visitor);

    /**
     * @param \SplObjectStorage $embeddedMap Map<Relation, mixed>
     * @param JsonSerializationVisitor $visitor
     * @param SerializationContext $context
     * @return mixed
     */
    public function serializeEmbedded(\SplObjectStorage $embeddedMap, JsonSerializationVisitor $visitor, SerializationContext $context);
}
