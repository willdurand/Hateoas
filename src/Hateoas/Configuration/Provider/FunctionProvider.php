<?php

namespace Hateoas\Configuration\Provider;

use Hateoas\Configuration\RelationProvider;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class FunctionProvider implements RelationProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getRelations(RelationProvider $configuration, string $class):array
    {
        if (!preg_match('/func\((?P<function>.+)\)/i', $configuration->getName(), $matches)) {
            return [];
        }

        return call_user_func($matches['function'], $class);
    }
}
