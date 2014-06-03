<?php

namespace Hateoas\Tests\Serializer;

use Hateoas\Tests\TestCase;

abstract class SerializerRegistryTest extends TestCase
{
    abstract public function mockSerializer();
    abstract public function createRegistry($defaultSerializerName = null);

    public function test()
    {
        $defaultSerializer = $this->mockSerializer();
        $registry = $this->createRegistry('hal');
        $registry->set('hal', $defaultSerializer);

        $this
            ->object($registry->get('hal'))
                ->isEqualTo($defaultSerializer)
            ->object($registry->get())
                ->isEqualTo($defaultSerializer)
            ->exception(function () use ($registry) {
                $registry->get('foo');
            })
                ->isInstanceOf('InvalidArgumentException')
                ->hasMessage('The "foo" serializer is not set. Available serializers are: hal.')

            ->when($registry->set('foo', $fooSerializer = $this->mockSerializer()))
            ->object($registry->get('foo'))
                ->isEqualTo($fooSerializer)
        ;
    }
}
