<?php

namespace Hateoas\Serializer\EventSubscriber;

use Hateoas\Factory\EmbeddedMapFactory;
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
     * @var EmbeddedMapFactory
     */
    private $embeddedMapFactory;

    public function __construct(
        XmlSerializerInterface $xmlSerializer, LinksFactory $linksFactory, EmbeddedMapFactory $embeddedMapFactory
    )
    {
        $this->xmlSerializer = $xmlSerializer;
        $this->linksFactory = $linksFactory;
        $this->embeddedMapFactory = $embeddedMapFactory;
    }

    public function onPostSerialize(ObjectEvent $event)
    {
        $embeddedMap = $this->embeddedMapFactory->create($event->getObject());
        $links = $this->linksFactory->createLinks($event->getObject());

        $this->xmlSerializer->serializeLinks($links, $event->getVisitor());
        $this->xmlSerializer->serializeEmbedded($embeddedMap, $event->getVisitor(), $event->getContext());
    }
}
