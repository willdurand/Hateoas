<?php

declare(strict_types=1);

namespace Hateoas\Configuration\Provider;

use Hateoas\Configuration\RelationProvider;
use JMS\Serializer\Expression\ExpressionEvaluatorInterface;

class ExpressionEvaluatorProvider implements RelationProviderInterface
{
    /**
     * @var ExpressionEvaluatorInterface
     */
    private $expressionEvaluator;

    public function __construct(ExpressionEvaluatorInterface $expressionEvaluator)
    {
        $this->expressionEvaluator = $expressionEvaluator;
    }

    /**
     * {@inheritDoc}
     */
    public function getRelations(RelationProvider $configuration, string $class): array
    {
        if (!preg_match('/expr\((?P<expression>.+)\)/', $configuration->getName(), $matches)) {
            return [];
        }

        return $this->expressionEvaluator->evaluate($matches['expression'], ['class' => $class]);
    }
}
