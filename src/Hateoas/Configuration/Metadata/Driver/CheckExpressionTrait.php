<?php

namespace Hateoas\Configuration\Metadata\Driver;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

trait CheckExpressionTrait
{
    /**
     * @var ExpressionLanguage
     */
    private $expressionLanguage;

    private function checkExpression($exp, array $names = [])
    {
        if (is_string($exp) && preg_match('/expr\((?P<expression>.+)\)/', $exp, $matches)) {

            $names = array_merge($names, ['object', 'context', 'metadata']);
            return $this->expressionLanguage->parse($matches['expression'], $names);
        } else {
            return $exp;
        }
    }

    public function checkExpressionArray(array $data)
    {
        $newArray = array();
        foreach ($data as $key => $value) {
            $newArray[$key] = $this->checkExpression($value);
        }
        return $newArray;
    }
}
