<?php

namespace Hateoas;

use Hateoas\Configuration\RelationsManager;
use Hateoas\Factory\LinkFactory;
use Hateoas\Factory\LinksFactory;
use Hateoas\Handler\HandlerManager;
use Hateoas\Serializer\EventSubscriber\LinkEventSubscriber;
use JMS\Serializer\EventDispatcher\EventDispatcherInterface;
use JMS\Serializer\SerializerBuilder;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class HateoasBuilder
{
    public static function create()
    {
        return new static();
    }

    public static function getSerializer()
    {
        $builder = static::create();

        return $builder->configureSerializerBuilder()->build();
    }

    /**
     * @var SerializerBuilder
     */
    private $serializerBuilder;

    public function __construct(SerializerBuilder $serializerBuilder = null)
    {
        $this->serializerBuilder = $serializerBuilder ?: SerializerBuilder::create();
    }

    public function configureSerializerBuilder()
    {
        $handlerManager = new HandlerManager();
        $relationsManager = new RelationsManager();
        $linkFactory = new LinkFactory($handlerManager);
        $linksFactory = new LinksFactory($relationsManager, $linkFactory);
        $linkEventSubscriber = new LinkEventSubscriber($linksFactory);

        $this->serializerBuilder
            ->addDefaultListeners()
            ->configureListeners(function (EventDispatcherInterface $dispatcher) use ($linkEventSubscriber) {
                $dispatcher->addSubscriber($linkEventSubscriber);
            })
        ;

        return $this->serializerBuilder;
    }
}
