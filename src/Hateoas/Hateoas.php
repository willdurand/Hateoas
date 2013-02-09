<?php

namespace Hateoas;

use Doctrine\Common\Annotations\AnnotationReader;
use Hateoas\Serializer\Handler;
use JMS\Serializer\Metadata\Driver\AnnotationDriver;
use JMS\Serializer\SerializerBuilder;
use Metadata\MetadataFactory;

/**
 * @author William Durand <william.durand1@gmail.com>
 */
class Hateoas
{
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
}
