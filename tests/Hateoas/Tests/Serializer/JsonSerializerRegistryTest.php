<?php

namespace Hateoas\Tests\Serializer;

use Hateoas\Serializer\JsonSerializerRegistry;

class JsonSerializerRegistryTest extends SerializerRegistryTest
{
    public function mockSerializer()
    {
        return $this->prophesize('Hateoas\Serializer\JsonSerializerInterface')->reveal();
    }

    public function createRegistry($defaultSerializerName = null)
    {
        return new JsonSerializerRegistry($defaultSerializerName);
    }
}
