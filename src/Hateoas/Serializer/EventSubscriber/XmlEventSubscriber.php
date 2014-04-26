<?php

namespace Hateoas\Serializer\EventSubscriber;

use Hateoas\Factory\EmbeddedsFactory;
use Hateoas\Factory\LinksFactory;
use Hateoas\Serializer\EmbedSerializer;
use Hateoas\Serializer\JMSSerializerMetadataAwareInterface;
use Hateoas\Serializer\XmlSerializerInterface;
use JMS\Serializer\EventDispatcher\Events;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use Metadata\MetadataFactoryInterface;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class XmlEventSubscriber implements EventSubscriberInterface, JMSSerializerMetadataAwareInterface
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
     * @var MetadataFactoryInterface
     */
    private $serializerMetadataFactory;

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

    public function setMetadataFactory(MetadataFactoryInterface $metadataFactory)
    {
        $this->serializerMetadataFactory = $metadataFactory;
    }

    public function onPostSerialize(ObjectEvent $event)
    {
        $object    = $event->getObject();
        $context   = $event->getContext();
        $embeddeds = $this->embeddedsFactory->create($event->getObject(), $event->getContext());
        $links     = $this->linksFactory->create($event->getObject(), $event->getContext());

        if (count($links) > 0) {
            $this->xmlSerializer->serializeLinks($links, $event->getVisitor(), $context);
        }

        if (count($embeddeds) > 0) {
            // This fixes the $context->getDepth()
            $context->startVisiting($object);
            $context->pushClassMetadata($this->serializerMetadataFactory->getMetadataForClass($event->getType()['name']));

            $this->xmlSerializer->serializeEmbeddeds($embeddeds, $event->getVisitor(), new EmbedSerializer($context));

            $context->stopVisiting($object);
            $context->popClassMetadata();
        }
    }
}
