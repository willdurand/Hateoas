<?php

namespace Hateoas\Serializer\EventSubscriber;

use Hateoas\Factory\EmbeddedsFactory;
use Hateoas\Factory\LinksFactory;
use Hateoas\Serializer\EmbedSerializer;
use Hateoas\Serializer\JMSSerializerMetadataAwareInterface;
use Hateoas\Serializer\JsonSerializerInterface;
use Hateoas\Serializer\Metadata\InlineDeferrer;
use Hateoas\Serializer\Metadata\RelationPropertyMetadata;
use JMS\Serializer\EventDispatcher\Events;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use Metadata\MetadataFactoryInterface;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class JsonEventSubscriber implements EventSubscriberInterface, JMSSerializerMetadataAwareInterface
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
     * @var EmbeddedsFactory
     */
    private $embeddedsFactory;

    /**
     * @var InlineDeferrer
     */
    private $embeddedsInlineDeferrer;

    /**
     * @var InlineDeferrer
     */
    private $linksInlineDeferrer;

    /**
     * @var MetadataFactoryInterface
     */
    private $serializerMetadataFactory;

    /**
     * @param JsonSerializerInterface $jsonSerializer
     * @param LinksFactory            $linksFactory
     * @param EmbeddedsFactory        $embeddedsFactory
     * @param InlineDeferrer          $embeddedsInlineDeferrer
     * @param InlineDeferrer          $linksInleDeferrer
     */
    public function __construct(
        JsonSerializerInterface $jsonSerializer,
        LinksFactory $linksFactory,
        EmbeddedsFactory $embeddedsFactory,
        InlineDeferrer $embeddedsInlineDeferrer,
        InlineDeferrer $linksInleDeferrer
    ) {
        $this->jsonSerializer          = $jsonSerializer;
        $this->linksFactory            = $linksFactory;
        $this->embeddedsFactory        = $embeddedsFactory;
        $this->embeddedsInlineDeferrer = $embeddedsInlineDeferrer;
        $this->linksInlineDeferrer     = $linksInleDeferrer;
    }

    public function setMetadataFactory(MetadataFactoryInterface $metadataFactory)
    {
        $this->serializerMetadataFactory = $metadataFactory;
    }

    public function onPostSerialize(ObjectEvent $event)
    {
        $object  = $event->getObject();
        $context = $event->getContext();

        $embeddeds = $this->embeddedsFactory->create($object, $context);
        $links     = $this->linksFactory->create($object, $context);

        $embeddeds = $this->embeddedsInlineDeferrer->handleItems($object, $embeddeds, $context);
        $links  = $this->linksInlineDeferrer->handleItems($object, $links, $context);

        if (count($links) > 0) {
            $this->jsonSerializer->serializeLinks($links, $event->getVisitor(), $context);
        }

        if (count($embeddeds) > 0) {
            // This fixes the $context->getDepth()
            $context->startVisiting($object);
            $context->pushClassMetadata($this->serializerMetadataFactory->getMetadataForClass($event->getType()['name']));

            $this->jsonSerializer->serializeEmbeddeds($embeddeds, $event->getVisitor(), new EmbedSerializer($context));

            $context->stopVisiting($object);
            $context->popClassMetadata();
        }
    }
}
