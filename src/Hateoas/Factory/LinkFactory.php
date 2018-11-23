<?php

namespace Hateoas\Factory;

use Hateoas\Configuration\Relation;
use Hateoas\Configuration\Route;
use Hateoas\Model\Link;
use Hateoas\UrlGenerator\UrlGeneratorRegistry;
use JMS\Serializer\Expression\ExpressionEvaluatorInterface;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class LinkFactory
{
    /**
     * @var ExpressionEvaluatorInterface
     */
    private $expressionEvaluator;

    /**
     * @var UrlGeneratorRegistry
     */
    private $urlGeneratorRegistry;

    /**
     * @param UrlGeneratorRegistry $urlGeneratorRegistry
     */
    public function __construct(UrlGeneratorRegistry $urlGeneratorRegistry, ExpressionEvaluatorInterface $expressionEvaluator = null)
    {
        $this->urlGeneratorRegistry = $urlGeneratorRegistry;
        $this->expressionEvaluator  = $expressionEvaluator;
    }

    public function setExpressionEvaluator(ExpressionEvaluatorInterface $expressionEvaluator)
    {
        $this->expressionEvaluator  = $expressionEvaluator;
    }
    /**
     * @param object   $object
     * @param Relation $relation
     *
     * @return Link
     */
    public function createLink($object, Relation $relation)
    {
        $data = ['object' => $object];

        $rel =  $this->checkExpression($relation->getName(), $data);
        $href = $this->checkExpression($relation->getHref(), $data);
        if ($href instanceof Route) {
            if (!$this->urlGeneratorRegistry->hasGenerators()) {
                throw new \RuntimeException('You cannot use a route without an url generator.');
            }

            $name       = $this->checkExpression($href->getName(), $data);
            $parameters = is_array($href->getParameters())
                ? $this->evaluateArray($this->expressionEvaluator, $href->getParameters(), $data)
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

        $attributes = $this->evaluateArray($this->expressionEvaluator, $relation->getAttributes(), $data);

        return new Link($rel, $href, $attributes);
    }

    const EXPRESSION_REGEX = '/expr\((?P<expression>.+)\)/';

    private function checkExpression($exp, array $data)
    {
        if (is_string($exp) && preg_match(self::EXPRESSION_REGEX, $exp, $matches)) {
            return $this->expressionEvaluator->evaluate($matches['expression'], $data);
        } else {
            return $exp;
        }
    }

    private function evaluateArray(ExpressionEvaluatorInterface $expressionEvaluator, array $array, array $data)
    {
        $newArray = array();
        foreach ($array as $key => $value) {
            $key = $this->checkExpression($key, $data);
            $value = is_array($value) ? $this->evaluateArray($expressionEvaluator, $value, $data) : $this->checkExpression($value, $data);

            $newArray[$key] = $value;
        }

        return $newArray;
    }
}
