<?php

namespace Hateoas\Serializer\EventSubscriber;

use Hateoas\Factory\EmbeddedMapFactory;
use Hateoas\Factory\LinksFactory;
use Hateoas\Serializer\JsonSerializerInterface;
use JMS\Serializer\EventDispatcher\Events;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class JsonEventSubscriber implements EventSubscriberInterface
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
     * @var JsonSerializerInterface
     */
    private $jsonSerializer;

    /**
     * @var LinksFactory
     */
    private $linksFactory;

    /**
     * @var EmbeddedMapFactory
     */
    private $embeddedMapFactory;

    public function __construct(
        JsonSerializerInterface $jsonSerializer, LinksFactory $linksFactory, EmbeddedMapFactory $embeddedMapFactory
    )
    {
        $this->jsonSerializer = $jsonSerializer;
        $this->linksFactory = $linksFactory;
        $this->embeddedMapFactory = $embeddedMapFactory;
    }

    public function onPostSerialize(ObjectEvent $event)
    {
        $embeddedMap = $this->embeddedMapFactory->create($event->getObject());
        $links = $this->linksFactory->createLinks($event->getObject());

        $this->jsonSerializer->serializeLinks($links, $event->getVisitor());
        $this->jsonSerializer->serializeEmbedded($embeddedMap, $event->getVisitor(), $event->getContext());
    }
}
