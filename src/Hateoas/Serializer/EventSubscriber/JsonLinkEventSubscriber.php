<?php

namespace Hateoas\Serializer\EventSubscriber;

use Hateoas\Factory\LinksFactory;
use Hateoas\Serializer\JsonSerializerInterface;
use JMS\Serializer\EventDispatcher\Events;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class JsonLinkEventSubscriber implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            array(
                'event' => Events::POST_SERIALIZE,
                'format' => 'json',
                'method' => 'onPostSerialize',
            ),
        );
    }

    /**
     * @var LinksFactory
     */
    private $linksFactory;

    /**
     * @var JsonSerializerInterface
     */
    private $jsonSerializer;

    public function __construct(LinksFactory $linksFactory, JsonSerializerInterface $jsonSerializer)
    {
        $this->linksFactory = $linksFactory;
        $this->jsonSerializer = $jsonSerializer;
    }

    public function onPostSerialize(ObjectEvent $event)
    {
        $links = $this->linksFactory->createLinks($event->getObject());
        $this->jsonSerializer->serializeLinks($links, $event->getVisitor());
    }
}
