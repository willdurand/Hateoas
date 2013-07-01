<?php

namespace Hateoas\Factory;

use Hateoas\Factory\Config\ConfigInterface;
use Hateoas\Factory\Definition\CollectionDefinition;
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
    private $resourceDefinitions;

    /**
     * @var array
     */
    private $collectionDefinitions;

    public function __construct(ConfigInterface $config)
    {
        $this->resourceDefinitions   = $config->getResourceDefinitions();
        $this->collectionDefinitions = $config->getCollectionDefinitions();
    }

    /**
     * {@inheritdoc}
     */
    public function getResourceDefinition($data)
    {
        foreach ($this->resourceDefinitions as $class => $definition) {
            if ((is_object($data) && !$data instanceof $class) || (!is_object($data) && !is_subclass_of($data, $class) && $data != $class)) {
                continue;
            }

            if (!$definition instanceof ResourceDefinition) {
                $this->resourceDefinitions[$class] = $this->createResourceDefinition($definition, $class);
            }

            return $this->resourceDefinitions[$class];
        }

        throw new \RuntimeException(sprintf('No definition found for resource "%s".', is_object($data) ? get_class($data) : $data));
    }

    /**
     * {@inheritdoc}
     */
    public function getCollectionDefinition($className)
    {
        foreach ($this->collectionDefinitions as $class => $definition) {
            if ($className !== $class) {
                continue;
            }

            if (!$definition instanceof CollectionDefinition) {
                $this->collectionDefinitions[$class] = $this->createCollectionDefinition($definition, $class);
            }

            return $this->collectionDefinitions[$class];
        }

        throw new \RuntimeException(sprintf('No definition found for collection of "%s".', $className));
    }

    protected function createLinkDefinition($definition, $class)
    {
        if (!is_array($definition)) {
            throw new \InvalidArgumentException(sprintf('A link definition should be an array in "%s".', $class));
        }

        if (!isset($definition['rel'])) {
            throw new \InvalidArgumentException(sprintf('A link definition should define a "rel" value in %s.', $class));
        }

        $type = isset($definition['type']) ? $definition['type'] : null;

        return new LinkDefinition($definition['rel'], $type);
    }

    private function createResourceDefinition(array $definition, $class)
    {
        $links = $this->createLinks($definition, $class);

        return new ResourceDefinition($class, $links);
    }

    private function createCollectionDefinition(array $definition, $class)
    {
        $links      = $this->createLinks($definition, $class);
        $attributes = isset($definition['attributes']) ? $definition['attributes'] : array();
        $rootName   = isset($definition['rootName'])   ? $definition['rootName']   : null;

        return new CollectionDefinition($class, $links, $attributes, $rootName);
    }

    private function createLinks(array $definition, $class)
    {
        $links = array();

        if (isset($definition['links'])) {
            if (!is_array($definition['links'])) {
                throw new \InvalidArgumentException(sprintf('The "link" definition should be an array in "%s".', $class, $class));
            }
            foreach ($definition['links'] as $link) {
                if (!$link instanceof LinkDefinition) {
                    $link = $this->createLinkDefinition($link, $class);
                }

                $links[] = $link;
            }
        }

        return $links;
    }
}
