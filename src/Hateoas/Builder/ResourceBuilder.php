<?php

namespace Hateoas\Builder;

use Hateoas\Resource;
use Hateoas\Factory\FactoryInterface;
use Hateoas\Builder\LinkBuilder;

class ResourceBuilder
{
    /**
     * @var Factory
     */
    private $factory;

    /**
     * @var LinkBuilder
     */
    private $linkBuilder;

    public function __construct(FactoryInterface $factory, LinkBuilder $linkBuilder)
    {
        $this->factory     = $factory;
        $this->linkBuilder = $linkBuilder;
    }

    /**
     * @param  object   $data
     * @return Resource
     */
    public function create($data)
    {
        $resourceDefinition = $this->factory->getResourceDefinition($data);

        $links = array();
        foreach ($resourceDefinition->getLinks() as $linkDefinition) {
            $links[] = $this->linkBuilder->createFromDefinition($linkDefinition, $data);
        }

        return new Resource($data, $links);
    }

    /**
     * @param \Traversable $collection
     * @param  string     $className
     * @return Collection
     */
    public function createCollection(\Traversable $collection, $className)
    {
        $collectionDefinition = $this->factory->getCollectionDefinition($className);

        $resources = array();
        foreach ($collection as $coll) {
            $resources[] = $this->create($coll);
        }

        $links = array();
        foreach ($collectionDefinition->getLinks() as $linkDefinition) {
            $links[] = $this->linkBuilder->createFromDefinition($linkDefinition, $data);
        }

        return new Collection($resources, $links);
    }
}
