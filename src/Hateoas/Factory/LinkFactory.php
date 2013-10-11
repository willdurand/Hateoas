<?php

namespace Hateoas\Factory;

use Hateoas\Configuration\Relation;
use Hateoas\Configuration\Route;
use Hateoas\Expression\ExpressionEvaluator;
use Hateoas\Model\Link;
use Hateoas\UrlGenerator\UrlGeneratorInterface;
use Hateoas\UrlGenerator\UrlGeneratorRegistry;

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
     * @var UrlGeneratorRegistry
     */
    private $urlGeneratorRegistry;

    /**
     * @param ExpressionEvaluator  $expressionEvaluator
     * @param UrlGeneratorRegistry $urlGeneratorRegistry
     */
    public function __construct(
        ExpressionEvaluator $expressionEvaluator,
        UrlGeneratorRegistry $urlGeneratorRegistry
    ) {
        $this->expressionEvaluator  = $expressionEvaluator;
        $this->urlGeneratorRegistry = $urlGeneratorRegistry;
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
            if (!$this->urlGeneratorRegistry->hasGenerators()) {
                throw new \RuntimeException('You cannot use a route without an url generator.');
            }

            $name       = $this->expressionEvaluator->evaluate($href->getName(), $object);
            $parameters = $this->expressionEvaluator->evaluateArray($href->getParameters(), $object);

            $href = $this->urlGeneratorRegistry
                ->get($href->getGenerator())
                ->generate($name, $parameters, $href->isAbsolute())
            ;
        } else {
            $href = $this->expressionEvaluator->evaluate($href, $object);
        }

        $attributes = $this->expressionEvaluator->evaluateArray($relation->getAttributes(), $object);

        return new Link($rel, $href, $attributes);
    }
}
