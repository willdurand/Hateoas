<?php

namespace Hateoas\Factory;

use Hateoas\Configuration\Relation;
use Hateoas\Configuration\Route;
use Hateoas\Model\Link;
use Hateoas\UrlGenerator\UrlGeneratorRegistry;
use JMS\Serializer\Expression\CompilableExpressionEvaluatorInterface;
use JMS\Serializer\Expression\Expression;
use JMS\Serializer\SerializationContext;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class LinkFactory
{
    /**
     * @var CompilableExpressionEvaluatorInterface
     */
    private $expressionEvaluator;

    /**
     * @var UrlGeneratorRegistry
     */
    private $urlGeneratorRegistry;

    /**
     * @param UrlGeneratorRegistry $urlGeneratorRegistry
     */
    public function __construct(UrlGeneratorRegistry $urlGeneratorRegistry, CompilableExpressionEvaluatorInterface $expressionEvaluator = null)
    {
        $this->urlGeneratorRegistry = $urlGeneratorRegistry;
        $this->expressionEvaluator  = $expressionEvaluator;
    }

    public function setExpressionEvaluator(CompilableExpressionEvaluatorInterface $expressionEvaluator)
    {
        $this->expressionEvaluator  = $expressionEvaluator;
    }
    /**
     * @param object   $object
     * @param Relation $relation
     *
     * @return Link
     */
    public function createLink($object, Relation $relation, SerializationContext $context)
    {
        $data = ['object' => $object, 'context' => $context];

        $rel =  $relation->getName();
        $href = $relation->getHref();
        if ($href instanceof Route) {
            if (!$this->urlGeneratorRegistry->hasGenerators()) {
                throw new \RuntimeException('You cannot use a route without an url generator.');
            }

            $name       = $this->checkExpression($href->getName(), $data);
            $parameters = is_array($href->getParameters())
                ? $this->evaluateArray($href->getParameters(), $data)
                : $this->checkExpression($href->getParameters(), $data)
            ;
            $isAbsolute = $this->checkExpression($href->isAbsolute(), $data);

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
            $href = $this->checkExpression($href, $data);
        }

        $attributes = $this->evaluateArray($relation->getAttributes(), $data);

        return new Link($rel, $href, $attributes);
    }

    private function checkExpression($exp, array $data)
    {
        if ($exp instanceof Expression) {
            return $this->expressionEvaluator->evaluateParsed($exp, $data);
        } else {
            return $exp;
        }
    }

    private function evaluateArray(array $array, array $data)
    {
        $newArray = array();
        foreach ($array as $key => $value) {
            $value = is_array($value) ? $this->evaluateArray($value, $data) : $this->checkExpression($value, $data);

            $newArray[$key] = $value;
        }

        return $newArray;
    }
}
