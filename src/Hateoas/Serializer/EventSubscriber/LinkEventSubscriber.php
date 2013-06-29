<?php

namespace Hateoas\Serializer\EventSubscriber;

use Hateoas\Factory\LinksFactory;
use JMS\Serializer\EventDispatcher\Events;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;


/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class LinkEventSubscriber implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        $methods = array();
        foreach (array('json', 'xml') as $format) {
            $methods[] = array(
                'event' => Events::POST_SERIALIZE,
                'format' => $format,
                'method' => 'onPostSerialize'.strtoupper($format),
            );
        }

        return $methods;
    }

    private $linksFactory;

    public function __construct(LinksFactory $linksFactory)
    {
        $this->linksFactory = $linksFactory;
    }

    public function onPostSerializeXML(ObjectEvent $event)
    {
        $links = $this->linksFactory->createLinks($event->getObject());


    }

    public function onPostSerializeJSON(ObjectEvent $event)
    {

    }
}
