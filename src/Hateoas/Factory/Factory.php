<?php

namespace Hateoas\Factory;

use Hateoas\Factory\Definition\ResourceDefinition;
use Hateoas\Factory\Definition\LinkDefinition;

/**
 * @author William Durand <william.durand1@gmail.com>
 */
class Factory implements FactoryInterface
{
    /**
     * @var array
     */
    private $definitions;

    public function __construct(array $definitions)
    {
        $this->definitions = $definitions;
    }

    /**
     * {@inheritdoc}
     */
    public function getResourceDefinition($data)
    {
        foreach ($this->definitions as $class => $definition) {
            if ((is_object($data) && !$data instanceof $class) || (!is_object($data) && !is_subclass_of($data, $class) && $data != $class)) {
                continue;
            }

            if (!$definition instanceof ResourceDefinition) {
                $this->definitions[$class] = $this->createDefinition($definition, $class);
            }

            return $this->definitions[$class];
        }

        throw new \RuntimeException(sprintf('No definition found for resource "%s".', is_object($data) ? get_class($data) : $data));
    }

    /**
     * {@inheritdoc}
     */
    public function getCollectionDefinition($data)
    {
    }

    protected function createLinkDefinition(array $definition)
    {
        if (!isset($definition['rel'])) {
            throw new \InvalidArgumentException('A link definition should define a "rel" value.');
        }

        $type = isset($definition['type']) ? $definition['type'] : null;

        return new LinkDefinition($definition['rel'], $type);
    }

    private function createDefinition(array $definition, $class)
    {
        $links = array();
        if (isset($definition['links'])) {
            foreach ($definition['links'] as $link) {
                if (!$link instanceof LinkDefinition) {
                    $link = $this->createLinkDefinition($link);
                }

                $links[] = $link;
            }
        }

        return new ResourceDefinition($class, $links);
    }
}
