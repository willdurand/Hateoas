<?php

namespace Hateoas\Configuration\Provider;

use Hateoas\Configuration\RelationProvider;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class StaticMethodProvider implements RelationProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getRelations(RelationProvider $configuration, string $class):array
    {
        if (!preg_match('/^(?P<class>[a-z0-9_\\\\]+)::(?P<method>[a-z0-9_]+)$/i', $configuration->getName(), $matches)) {
            return [];
        }

        if ($matches['class'] === 'self') {
            $matches['class'] = $class;
        }

        return call_user_func(array($matches['class'], $matches['method']), $class);
    }
}
