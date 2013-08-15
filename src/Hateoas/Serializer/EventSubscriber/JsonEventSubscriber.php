<?php

namespace Hateoas\Serializer\EventSubscriber;

use Hateoas\Factory\EmbedsFactory;
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
                'event'  => Events::POST_SERIALIZE,
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
     * @var EmbedsFactory
     */
    private $embedsFactory;

    /**
     * @param JsonSerializerInterface $jsonSerializer
     * @param LinksFactory            $linksFactory
     * @param EmbedsFactory           $embedsFactory
     */
    public function __construct(JsonSerializerInterface $jsonSerializer, LinksFactory $linksFactory, EmbedsFactory $embedsFactory)
    {
        $this->jsonSerializer = $jsonSerializer;
        $this->linksFactory   = $linksFactory;
        $this->embedsFactory  = $embedsFactory;
    }

    public function onPostSerialize(ObjectEvent $event)
    {
        $embeds = $this->embedsFactory->create($event->getObject());
        $links  = $this->linksFactory->createLinks($event->getObject());

        if (count($links) > 0) {
            $this->jsonSerializer->serializeLinks($links, $event->getVisitor());
        }

        if (count($embeds) > 0) {
            $this->jsonSerializer->serializeEmbedded($embeds, $event->getVisitor(), $event->getContext());
        }
    }
}
