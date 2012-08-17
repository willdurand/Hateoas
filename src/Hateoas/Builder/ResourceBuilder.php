<?php

namespace Hateoas\Builder;

use Hateoas\Resource;
use Hateoas\Factory\Factory;
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

    public function __construct(Factory $factory, LinkBuilder $linkBuilder)
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
}
