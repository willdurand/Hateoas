<?php

namespace Hateoas\Serializer;

use Hateoas\Model\Embedded;
use Hateoas\Model\Link;
use JMS\Serializer\JsonSerializationVisitor;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Visitor\SerializationVisitorInterface;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
interface JsonSerializerInterface
{
    /**
     * @param Link[]                   $links
     * @param JsonSerializationVisitor $visitor
     * @param SerializationContext     $context
     */
    public function serializeLinks(array $links, SerializationVisitorInterface $visitor, SerializationContext $context);

    /**
     * @param Embedded[]               $embeddeds
     * @param JsonSerializationVisitor $visitor
     * @param SerializationContext     $context
     */
    public function serializeEmbeddeds(array $embeddeds, SerializationVisitorInterface $visitor, SerializationContext $context);
}
