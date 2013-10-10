<?php

namespace Hateoas\Factory;

use Hateoas\Configuration\Relation;
use Hateoas\Configuration\Route;
use Hateoas\Expression\ExpressionEvaluator;
use Hateoas\Model\Link;
use Hateoas\UrlGenerator\UrlGeneratorInterface;

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
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @param ExpressionEvaluator        $expressionEvaluator
     * @param UrlGeneratorInterface|null $urlGenerator
     */
    public function __construct(
        ExpressionEvaluator $expressionEvaluator,
        UrlGeneratorInterface $urlGenerator = null
    )
    {
        $this->expressionEvaluator = $expressionEvaluator;
        $this->urlGenerator        = $urlGenerator;
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
            if (null === $this->urlGenerator) {
                throw new \RuntimeException('You cannot use a route without an url generator.');
            }

            $name       = $this->expressionEvaluator->evaluate($href->getName(), $object);
            $parameters = $this->expressionEvaluator->evaluateArray($href->getParameters(), $object);

            $href = $this->urlGenerator->generate($name, $parameters, $href->isAbsolute());
        } else {
            $href = $this->expressionEvaluator->evaluate($href, $object);
        }

        $attributes = $this->expressionEvaluator->evaluateArray($relation->getAttributes(), $object);

        return new Link($rel, $href, $attributes);
    }
}
