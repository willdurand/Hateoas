<?php

namespace Hateoas\Factory;

use Hateoas\Configuration\Relation;
use Hateoas\Configuration\Route;
use Hateoas\Expression\ExpressionEvaluator;
use Hateoas\Model\Link;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class LinkFactory
{
    /**
     * @var ExpressionEvaluator
     */
    private $expressionEvaluator;

    /**
     * @var RouteFactoryInterface
     */
    private $routeFactory;

    /**
     * @param ExpressionEvaluator        $expressionEvaluator
     * @param RouteFactoryInterface|null $routeFactory
     */
    public function __construct(
        ExpressionEvaluator $expressionEvaluator,
        RouteFactoryInterface $routeFactory = null
    )
    {
        $this->expressionEvaluator = $expressionEvaluator;
        $this->routeFactory   = $routeFactory;
    }

    /**
     * @param object   $object
     * @param Relation $relation
     *
     * @return Link
     */
    public function createLink($object, Relation $relation)
    {
        $rel =  $this->expressionEvaluator->evaluate($relation->getName(), $object);
        $href = $relation->getHref();

        if ($href instanceof Route) {
            if (null === $this->routeFactory) {
                throw new \RuntimeException('You cannot use a route without a route factory.');
            }

            $name       = $this->expressionEvaluator->evaluate($href->getName(), $object);
            $parameters = $this->expressionEvaluator->evaluateArray($href->getParameters(), $object);

            $href = $this->routeFactory->create($name, $parameters, $href->isAbsolute());
        } else {
            $href = $this->expressionEvaluator->evaluate($href, $object);
        }

        $attributes = $this->expressionEvaluator->evaluateArray($relation->getAttributes(), $object);

        return new Link($rel, $href, $attributes);
    }
}
