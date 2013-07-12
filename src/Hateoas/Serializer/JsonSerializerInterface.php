<?php

namespace Hateoas\Serializer;

use Hateoas\Model\Embed;
use Hateoas\Model\Link;
use Hateoas\Model\Resource;
use JMS\Serializer\JsonSerializationVisitor;
use JMS\Serializer\SerializationContext;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
interface JsonSerializerInterface
{
    /**
     * @param  Link[]                   $links
     * @param  JsonSerializationVisitor $visitor
     * @return void
     */
    public function serializeLinks(array $links, JsonSerializationVisitor $visitor);

    /**
     * @param  Embed[]                  $embeds
     * @param  JsonSerializationVisitor $visitor
     * @param  SerializationContext     $context
     * @return void
     */
    public function serializeEmbedded(array $embeds, JsonSerializationVisitor $visitor, SerializationContext $context);

    /**
     * @param  Resource                 $resource
     * @param  JsonSerializationVisitor $visitor
     * @param  SerializationContext     $context
     * @return array
     */
    public function serializeResource(Resource $resource, JsonSerializationVisitor $visitor, SerializationContext $context);
}
