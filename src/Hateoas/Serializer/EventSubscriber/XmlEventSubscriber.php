<?php

namespace Hateoas\Serializer\EventSubscriber;

use Hateoas\Factory\EmbedsFactory;
use Hateoas\Factory\LinksFactory;
use Hateoas\Serializer\XmlSerializerInterface;
use JMS\Serializer\EventDispatcher\Events;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class XmlEventSubscriber implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            array(
                'event' => Events::POST_SERIALIZE,
                'format' => 'xml',
                'method' => 'onPostSerialize',
            ),
        );
    }

    /**
     * @var XmlSerializerInterface
     */
    private $xmlSerializer;

    /**
     * @var LinksFactory
     */
    private $linksFactory;

    /**
     * @var EmbedsFactory
     */
    private $embedsFactory;

    public function __construct(
        XmlSerializerInterface $xmlSerializer, LinksFactory $linksFactory, EmbedsFactory $embedsFactory
    )
    {
        $this->xmlSerializer = $xmlSerializer;
        $this->linksFactory = $linksFactory;
        $this->embedsFactory = $embedsFactory;
    }

    public function onPostSerialize(ObjectEvent $event)
    {
        $embeds = $this->embedsFactory->create($event->getObject());
        $links = $this->linksFactory->createLinks($event->getObject());

        if (count($links) > 0) {
            $this->xmlSerializer->serializeLinks($links, $event->getVisitor());
        }
        if (count($embeds) > 0) {
            $this->xmlSerializer->serializeEmbedded($embeds, $event->getVisitor(), $event->getContext());
        }
    }
}
