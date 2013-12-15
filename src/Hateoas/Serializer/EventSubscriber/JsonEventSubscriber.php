<?php

namespace Hateoas\Serializer\EventSubscriber;

use Hateoas\Factory\EmbedsFactory;
use Hateoas\Factory\LinksFactory;
use Hateoas\Serializer\JsonSerializerInterface;
use Hateoas\Serializer\Metadata\InlineDeferrer;
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
     * @var InlineDeferrer
     */
    private $embedsInlineDeferrer;

    /**
     * @var InlineDeferrer
     */
    private $linksInlineDeferrer;

    /**
     * @param JsonSerializerInterface $jsonSerializer
     * @param LinksFactory            $linksFactory
     * @param EmbedsFactory           $embedsFactory
     * @param InlineDeferrer          $embedsInlineDeferrer
     * @param InlineDeferrer          $linksInleDeferrer
     */
    public function __construct(
        JsonSerializerInterface $jsonSerializer,
        LinksFactory $linksFactory,
        EmbedsFactory $embedsFactory,
        InlineDeferrer $embedsInlineDeferrer,
        InlineDeferrer $linksInleDeferrer
    ) {
        $this->jsonSerializer       = $jsonSerializer;
        $this->linksFactory         = $linksFactory;
        $this->embedsFactory        = $embedsFactory;
        $this->embedsInlineDeferrer = $embedsInlineDeferrer;
        $this->linksInlineDeferrer  = $linksInleDeferrer;
    }

    public function onPostSerialize(ObjectEvent $event)
    {
        $object  = $event->getObject();
        $context = $event->getContext();

        $embeds = $this->embedsFactory->create($object, $context);
        $links  = $this->linksFactory->createLinks($object, $context);

        $embeds = $this->embedsInlineDeferrer->handleItems($object, $embeds, $context);
        $links  = $this->linksInlineDeferrer->handleItems($object, $links, $context);

        if (count($links) > 0) {
            $this->jsonSerializer->serializeLinks($links, $event->getVisitor(), $context);
        }

        if (count($embeds) > 0) {
            $this->jsonSerializer->serializeEmbedded($embeds, $event->getVisitor(), $context);
        }
    }
}
