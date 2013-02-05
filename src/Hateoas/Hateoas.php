<?php

namespace Hateoas;

use JMS\Serializer\SerializerBuilder;

/**
 * @author William Durand <william.durand1@gmail.com>
 */
class Hateoas
{
    public static function getSerializer()
    {
        return SerializerBuilder::create()->build();
    }
}
