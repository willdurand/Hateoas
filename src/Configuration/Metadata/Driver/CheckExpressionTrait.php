<?php

declare(strict_types=1);

namespace Hateoas\Configuration\Metadata\Driver;

use JMS\Serializer\Expression\CompilableExpressionEvaluatorInterface;
use JMS\Serializer\Expression\Expression;

trait CheckExpressionTrait
{
    /**
     * @var CompilableExpressionEvaluatorInterface
     */
    private $expressionLanguage;

    /**
     * @param mixed $exp
     *
     * @return Expression|mixed
     */
    private function checkExpression($exp, array $names = [])
    {
        if (is_string($exp) && preg_match('/expr\((?P<expression>.+)\)/', $exp, $matches)) {
            $names = array_merge($names, ['object', 'context', 'metadata']);

            return $this->expressionLanguage->parse($matches['expression'], $names);
        } else {
            return $exp;
        }
    }

    private function checkExpressionArray(array $data): array
    {
        $newArray = [];
        foreach ($data as $key => $value) {
            $newArray[$key] = $this->checkExpression($value);
        }

        return $newArray;
    }
}
