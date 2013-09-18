<?php

namespace Hateoas\Factory;

use Hateoas\Factory\Definition\RouteLinkDefinition;

/**
 * @author William Durand <william.durand1@gmail.com>
 */
class RouteAwareFactory extends Factory
{
    /**
     * {@inheritdoc}
     */
    public function createLinkDefinition($definition, $class)
    {
        if (!is_array($definition)) {
            throw new \InvalidArgumentException(sprintf('A link definition should be an array in "%s".', $class));
        }

        if (!isset($definition['route'])) {
            throw new \InvalidArgumentException('A link definition should define a "route" value.');
        }

        if (!isset($definition['rel'])) {
            throw new \InvalidArgumentException('A link definition should define a "rel" value.');
        }

        $parameters = isset($definition['parameters']) ? $definition['parameters'] : array();
        $type = isset($definition['type']) ? $definition['type'] : null;

        return new RouteLinkDefinition(
            $definition['route'],
            $parameters,
            $definition['rel'],
            $type
        );
    }
}
