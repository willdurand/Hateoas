<?php

namespace Hateoas\Serializer\EventSubscriber;

use Hateoas\Factory\EmbeddedsFactory;
use Hateoas\Factory\LinksFactory;
use Hateoas\Serializer\HateoasSerializationContext;
use Hateoas\Serializer\XmlSerializerRegistry;
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
     * @var XmlSerializerRegistry
     */
    private $serializerRegistry;

    /**
     * @var LinksFactory
     */
    private $linksFactory;

    /**
     * @var EmbeddedsFactory
     */
    private $embeddedsFactory;

    /**
     * @param XmlSerializerRegistry $serializerRegistry
     * @param LinksFactory          $linksFactory
     * @param EmbeddedsFactory      $embeddedsFactory
     */
    public function __construct(XmlSerializerRegistry $serializerRegistry, LinksFactory $linksFactory, EmbeddedsFactory $embeddedsFactory)
    {
        $this->serializerRegistry = $serializerRegistry;
        $this->linksFactory       = $linksFactory;
        $this->embeddedsFactory   = $embeddedsFactory;
    }

    public function onPostSerialize(ObjectEvent $event)
    {
        $context   = $event->getContext();
        $embeddeds = $this->embeddedsFactory->create($event->getObject(), $event->getContext());
        $links     = $this->linksFactory->create($event->getObject(), $event->getContext());

        $serializerName = null;
        if ($context instanceof HateoasSerializationContext) {
            $serializerName = $context->getXmlSerializerName();
        }
        $xmlSerializer = $this->serializerRegistry->get($serializerName);

        if (count($links) > 0) {
            $xmlSerializer->serializeLinks($links, $event->getVisitor(), $context);
        }

        if (count($embeddeds) > 0) {
            $xmlSerializer->serializeEmbeddeds($embeddeds, $event->getVisitor(), $context);
        }
    }
}
