<?php

namespace Hateoas\Serializer;

use Hateoas\Model\Link;
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
}
