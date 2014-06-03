<?php

namespace Hateoas\Tests\Serializer;

use Hateoas\Serializer\XmlSerializerRegistry;

class XmlSerializerRegistryTest extends SerializerRegistryTest
{
    public function mockSerializer()
    {
        return $this->prophesize('Hateoas\Serializer\XmlSerializerInterface')->reveal();
    }

    public function createRegistry($defaultSerializerName = null)
    {
        return new XmlSerializerRegistry($defaultSerializerName);
    }
}
