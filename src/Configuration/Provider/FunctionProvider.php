<?php

declare(strict_types=1);

namespace Hateoas\Configuration\Provider;

use Hateoas\Configuration\RelationProvider;

class FunctionProvider implements RelationProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function getRelations(RelationProvider $configuration, string $class): array
    {
        if (!preg_match('/func\((?P<function>.+)\)/i', $configuration->getName(), $matches)) {
            return [];
        }

        return call_user_func($matches['function'], $class);
    }
}
