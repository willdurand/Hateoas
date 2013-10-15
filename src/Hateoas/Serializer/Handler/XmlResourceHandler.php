<?php

namespace Hateoas\Serializer\Handler;

use Hateoas\Representation\Resource;
use Hateoas\Serializer\XmlSerializerInterface;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\XmlSerializationVisitor;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class XmlResourceHandler implements SubscribingHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribingMethods()
    {
        return array(
            array(
                'format'    => 'xml',
                'type'      => 'Hateoas\Representation\Resource',
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'method'    => 'serializeToXml',
            ),
        );
    }

    /**
     * @var XmlSerializerInterface
     */
    private $xmlSerializer;

    /**
     * @param XmlSerializerInterface $xmlSerializer
     */
    public function __construct(XmlSerializerInterface $xmlSerializer)
    {
        $this->xmlSerializer = $xmlSerializer;
    }

    public function serializeToXml(XmlSerializationVisitor $visitor, Resource $resource, array $type, SerializationContext $context)
    {
        $this->xmlSerializer->serializeResource($resource, $visitor, $context);
    }
}
