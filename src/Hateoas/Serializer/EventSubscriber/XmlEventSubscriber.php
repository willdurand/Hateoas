<?php

namespace Hateoas\Serializer\EventSubscriber;

use Hateoas\Factory\EmbeddedsFactory;
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
                'event'  => Events::POST_SERIALIZE,
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
     * @var EmbeddedsFactory
     */
    private $embeddedsFactory;

    /**
     * @param XmlSerializerInterface $xmlSerializer
     * @param LinksFactory           $linksFactory
     * @param EmbeddedsFactory       $embeddedsFactory
     */
    public function __construct(XmlSerializerInterface $xmlSerializer, LinksFactory $linksFactory, EmbeddedsFactory $embeddedsFactory)
    {
        $this->xmlSerializer    = $xmlSerializer;
        $this->linksFactory     = $linksFactory;
        $this->embeddedsFactory = $embeddedsFactory;
    }

    public function onPostSerialize(ObjectEvent $event)
    {
        $object  = $event->getObject();
        $context = $event->getContext();

        $context->startVisiting($object);
        
        $embeddeds = $this->embeddedsFactory->create($object, $context);
        $links     = $this->linksFactory->create($object, $context);

        if (count($links) > 0) {
            $this->xmlSerializer->serializeLinks($links, $event->getVisitor(), $context);
        }

        if (count($embeddeds) > 0) {
            $this->xmlSerializer->serializeEmbeddeds($embeddeds, $event->getVisitor(), $context);
        }

        $context->stopVisiting($object);
    }
}
