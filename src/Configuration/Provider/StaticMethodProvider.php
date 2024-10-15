<?php

declare(strict_types=1);

namespace Hateoas\Configuration\Provider;

use Hateoas\Configuration\RelationProvider;

class StaticMethodProvider implements RelationProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function getRelations(RelationProvider $configuration, string $class): array
    {
        if (!preg_match('/^(?P<class>[a-z0-9_\\\\]+)::(?P<method>[a-z0-9_]+)$/i', $configuration->getName(), $matches)) {
            return [];
        }

        if ('self' === $matches['class']) {
            $matches['class'] = $class;
        }

        return call_user_func([$matches['class'], $matches['method']], $class);
    }
}
