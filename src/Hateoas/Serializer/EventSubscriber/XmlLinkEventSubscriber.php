<?php

namespace Hateoas\Serializer\EventSubscriber;

use Hateoas\Factory\LinksFactory;
use Hateoas\Serializer\XmlSerializerInterface;
use JMS\Serializer\EventDispatcher\Events;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class XmlLinkEventSubscriber implements EventSubscriberInterface
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
     * @var LinksFactory
     */
    private $linksFactory;

    /**
     * @var XmlSerializerInterface
     */
    private $xmlSerializer;

    public function __construct(LinksFactory $linksFactory, XmlSerializerInterface $xmlSerializer)
    {
        $this->linksFactory = $linksFactory;
        $this->xmlSerializer = $xmlSerializer;
    }

    public function onPostSerialize(ObjectEvent $event)
    {
        $links = $this->linksFactory->createLinks($event->getObject());
        $this->xmlSerializer->serializeLinks($links, $event->getVisitor());
    }
}
