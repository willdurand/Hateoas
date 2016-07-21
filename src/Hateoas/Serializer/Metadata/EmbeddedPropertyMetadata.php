<?php

namespace Hateoas\Serializer\Metadata;

use JMS\Serializer\Metadata\PropertyMetadata;

class EmbeddedPropertyMetadata extends PropertyMetadata
{
    public function __construct()
    {
    }

    public function serialize()
    {
        throw new \Exception();
    }

    public function unserialize($str)
    {
        throw new \Exception();
    }
}
