<?php

namespace Hateoas\Factory\Config;

/**
 * @author William Durand <william.durand1@gmail.com>
 */
class ArrayConfig implements ConfigInterface
{
    /**
     * @var array
     */
    private $resourceDefinitions;

    /**
     * @var array
     */
    private $collectionDefinitions;

    public function __construct(array $resourceDefinitions = array(), array $collectionDefinitions = array())
    {
        $this->resourceDefinitions   = $resourceDefinitions;
        $this->collectionDefinitions = $collectionDefinitions;
    }

    /**
     * {@inheritDoc}
     */
    public function getResourceDefinitions()
    {
        return $this->resourceDefinitions;
    }

    /**
     * {@inheritDoc}
     */
    public function getCollectionDefinitions()
    {
        return $this->collectionDefinitions;
    }
}
