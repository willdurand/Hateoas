<?php

namespace tests\Hateoas\Serializer\Handler;

use tests\TestCase;
use Hateoas\Serializer\Handler\JsonResourceHandler as TestedJsonResourceHandler;

class JsonResourceHandler extends TestCase
{
    public function testSerializeToJson()
    {
        $expectedResult = array(1, 2, 3);

        $this->mockGenerator->orphanize('__construct');
        $serializer = new \mock\Hateoas\Serializer\JsonSerializerInterface();
        $serializer->getMockController()->serializeResource = function () use ($expectedResult) {
            return $expectedResult;
        };

        $this->mockGenerator->orphanize('__construct');
        $resource = new \mock\Hateoas\Representation\Resource();

        $this->mockGenerator->orphanize('__construct');
        $visitor = new \mock\JMS\Serializer\JsonSerializationVisitor();

        $this->mockGenerator->orphanize('__construct');
        $context = new \mock\JMS\Serializer\SerializationContext();

        $handler = new TestedJsonResourceHandler($serializer);

        $result = $handler->serializeToJson($visitor, $resource, array(), $context);

        $this
            ->array($result)
                ->isEqualTo($expectedResult)
            ->mock($serializer)
                ->call('serializeResource')
                    ->withArguments($resource, $visitor, $context)
                    ->once()
        ;
    }
}
