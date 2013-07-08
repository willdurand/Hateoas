<?php

namespace Hateoas\Factory;

use Hateoas\Configuration\Relation;
use Hateoas\Configuration\Route;
use Hateoas\Handler\HandlerManager;
use Hateoas\Model\Link;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class LinkFactory
{
    /**
     * @var HandlerManager
     */
    private $handlerManager;

    /**
     * @var RouteFactoryInterface
     */
    private $routeFactory;

    public function __construct(HandlerManager $handlerManager, RouteFactoryInterface $routeFactory = null)
    {
        $this->handlerManager = $handlerManager;
        $this->routeFactory = $routeFactory;
    }

    /**
     * @param object $object
     * @param Relation $relation
     * @return Link
     */
    public function createLink($object, Relation $relation)
    {
        $rel = $this->handlerManager->transform($relation->getName(), $object);

        $href = $relation->getHref();
        if ($href instanceof Route) {
            if (null === $this->routeFactory) {
                throw new \RuntimeException('You cannot use route without a route factory.');
            }

            $name = $this->handlerManager->transform($href->getName(), $object);
            $parameters = $this->handlerManager->transformArray($href->getParameters(), $object);

            $href = $this->routeFactory->create($name, $parameters);
        } else {
            $href = $this->handlerManager->transform($href, $object);
        }

        $attributes = $this->handlerManager->transformArray($relation->getAttributes(), $object);

        return new Link($rel, $href, $attributes);
    }
}
