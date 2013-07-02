<?php

namespace Hateoas\Serializer\EventSubscriber;

use Hateoas\Factory\LinksFactory;
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

    private $linksFactory;

    public function __construct(LinksFactory $linksFactory)
    {
        $this->linksFactory = $linksFactory;
    }

    public function onPostSerialize(ObjectEvent $event)
    {

    }
}
