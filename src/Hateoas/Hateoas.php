<?php

namespace Hateoas;

use Doctrine\Common\Annotations\AnnotationReader;
use Hateoas\Serializer\Handler;
use JMS\Serializer\Metadata\Driver\AnnotationDriver;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\EventDispatcher;
use Metadata\MetadataFactory;

/**
 * @author William Durand <william.durand1@gmail.com>
 */
class Hateoas
{
    protected $metadataDirs = array();

    protected $debug = false;

    protected $handler;

    protected $subscribers = array();

    public static function getSerializer(array $metadataDirs = array(), $debug = false)
    {
        return SerializerBuilder::create()
            ->setDebug($debug)
            ->addMetadataDirs($metadataDirs)
            ->addDefaultHandlers()
            ->configureHandlers(function ($handlerRegistry) {
                $metadataFactory = new MetadataFactory(
                    new AnnotationDriver(new AnnotationReader())
                );
                $handlerRegistry->registerSubscribingHandler(new Handler($metadataFactory));
            })
            ->build();
    }

    public function setMetadataDirs(array $metadataDirs)
    {
        $this->metadataDirs = $metadataDirs;

        return $this;
    }

    public function setDebug($debug)
    {
        $this->debug = $debug;

        return $this;
    }

    public function setHandler(SubscribingHandlerInterface $handler)
    {
        $this->handler = $handler;

        return $this;
    }

    public function registerSubscriber(EventSubscriberInterface $subscriber)
    {
        $this->subscribers[] = $subscriber;

        return $this;
    }

    public function build()
    {
        $handler = $this->handler;

        $builder = SerializerBuilder::create()
            ->setDebug($this->debug)
            ->addMetadataDirs($this->metadataDirs)
            ->addDefaultHandlers()
            ->configureHandlers(function ($handlerRegistry) use ($handler) {
                if (!$handler) {
                    $metadataFactory = new MetadataFactory(
                          new AnnotationDriver(new AnnotationReader())
                    );
                    $handler = new Handler($metadataFactory);
                }
                $handlerRegistry->registerSubscribingHandler($handler);
            });

        $subscribers = $this->subscribers;
        $builder->configureListeners(function(EventDispatcher $dispatcher) use ($subscribers) {
            foreach ($subscribers as $subscriber) {
                $dispatcher->addSubscriber($subscriber);
            }
        });

        return $builder->build();
    }
}
