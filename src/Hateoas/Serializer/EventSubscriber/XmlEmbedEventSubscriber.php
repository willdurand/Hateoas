<?php

namespace Hateoas\Serializer\EventSubscriber;

use Hateoas\Factory\EmbeddedMapFactory;
use Hateoas\Serializer\XmlSerializerInterface;
use JMS\Serializer\EventDispatcher\Events;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class XmlEmbedEventSubscriber implements EventSubscriberInterface
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
     * @var EmbeddedMapFactory
     */
    private $embeddedMapFactory;

    /**
     * @var XmlSerializerInterface
     */
    private $xmlSerializer;

    public function __construct(EmbeddedMapFactory $embeddedMapFactory, XmlSerializerInterface $xmlSerializer)
    {
        $this->embeddedMapFactory = $embeddedMapFactory;
        $this->xmlSerializer = $xmlSerializer;
    }

    public function onPostSerialize(ObjectEvent $event)
    {
        $embeddedMap = $this->embeddedMapFactory->create($event->getObject());
        $this->xmlSerializer->serializeEmbedded($embeddedMap, $event->getVisitor(), $event->getContext());
    }
}
