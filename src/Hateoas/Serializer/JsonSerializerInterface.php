<?php

namespace Hateoas\Serializer;

use Hateoas\Model\Link;
use JMS\Serializer\JsonSerializationVisitor;

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
}
