<?php

namespace Hateoas\Factory;

use Hateoas\Configuration\Relation;
use Hateoas\Configuration\Route;
use Hateoas\Expression\ExpressionEvaluator;
use Hateoas\Model\Link;
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
    public function __construct(ExpressionEvaluator $expressionEvaluator, UrlGeneratorRegistry $urlGeneratorRegistry)
    {
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
            $parameters = is_array($href->getParameters())
                ? $this->expressionEvaluator->evaluateArray($href->getParameters(), $object)
                : $this->expressionEvaluator->evaluate($href->getParameters(), $object)
            ;
            $isAbsolute = $this->expressionEvaluator->evaluate($href->isAbsolute(), $object);

            if (!is_array($parameters)) {
                throw new \RuntimeException(
                    sprintf(
                        'The route parameters should be an array, %s given. Maybe you forgot to wrap the expression in expr(...).',
                        gettype($parameters)
                    )
                );
            }

            $href = $this->urlGeneratorRegistry
                ->get($href->getGenerator())
                ->generate($name, $parameters, $isAbsolute)
            ;
        } else {
            $href = $this->expressionEvaluator->evaluate($href, $object);
        }

        $attributes = $this->expressionEvaluator->evaluateArray($relation->getAttributes(), $object);

        return new Link($rel, $href, $attributes);
    }
}
