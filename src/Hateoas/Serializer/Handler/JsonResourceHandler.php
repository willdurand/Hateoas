<?php

namespace Hateoas\Serializer\Handler;

use Hateoas\Model\Resource;
use Hateoas\Serializer\JsonSerializerInterface;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\JsonSerializationVisitor;
use JMS\Serializer\SerializationContext;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class JsonResourceHandler implements SubscribingHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribingMethods()
    {
        return array(
            array(
                'format' => 'json',
                'type' => 'Hateoas\Model\Resource',
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'method' => 'serializeToJson',
            ),
        );
    }

    /**
     * @var JsonSerializerInterface
     */
    private $jsonSerializer;

    public function __construct(JsonSerializerInterface $jsonSerializer)
    {
        $this->jsonSerializer = $jsonSerializer;
    }

    public function serializeToJson(JsonSerializationVisitor $visitor, Resource $resource, array $type, SerializationContext $context)
    {
        return $this->jsonSerializer->serializeResource($resource, $visitor, $context);
    }
}
