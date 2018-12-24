<?php

declare(strict_types=1);

namespace Hateoas\Serializer\EventSubscriber;

use Hateoas\Factory\EmbeddedsFactory;
use Hateoas\Factory\LinksFactory;
use Hateoas\Serializer\SerializerInterface;
use JMS\Serializer\EventDispatcher\Events;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;

class XmlEventSubscriber implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            [
                'event'  => Events::POST_SERIALIZE,
                'format' => 'xml',
                'method' => 'onPostSerialize',
            ],
        ];
    }

    /**
     * @var SerializerInterface
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

    public function __construct(SerializerInterface $xmlSerializer, LinksFactory $linksFactory, EmbeddedsFactory $embeddedsFactory)
    {
        $this->xmlSerializer    = $xmlSerializer;
        $this->linksFactory     = $linksFactory;
        $this->embeddedsFactory = $embeddedsFactory;
    }

    public function onPostSerialize(ObjectEvent $event): void
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
