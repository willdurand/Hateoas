<?php

namespace Hateoas\Serializer\EventSubscriber;

use Hateoas\Factory\EmbeddedMapFactory;
use Hateoas\Serializer\JsonSerializerInterface;
use JMS\Serializer\EventDispatcher\Events;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class JsonEmbedEventSubscriber implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            array(
                'event' => Events::POST_SERIALIZE,
                'format' => 'json',
                'method' => 'onPostSerialize',
            ),
        );
    }

    /**
     * @var EmbeddedMapFactory
     */
    private $embeddedMapFactory;

    /**
     * @var JsonSerializerInterface
     */
    private $jsonSerializer;

    public function __construct(EmbeddedMapFactory $embeddedMapFactory, JsonSerializerInterface $jsonSerializer)
    {
        $this->embeddedMapFactory = $embeddedMapFactory;
        $this->jsonSerializer = $jsonSerializer;
    }

    public function onPostSerialize(ObjectEvent $event)
    {
        $embeddedMap = $this->embeddedMapFactory->create($event->getObject());
        $this->jsonSerializer->serializeEmbeddedMap($embeddedMap, $event->getVisitor(), $event->getContext());
    }
}
