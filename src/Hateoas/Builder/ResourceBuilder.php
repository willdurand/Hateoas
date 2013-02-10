<?php

namespace Hateoas\Builder;

use Hateoas\Collection;
use Hateoas\Resource;
use Hateoas\Factory\FactoryInterface;
use Hateoas\Builder\LinkBuilderInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * @author William Durand <william.durand1@gmail.com>
 */
class ResourceBuilder implements ResourceBuilderInterface
{
    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @var LinkBuilderInterface
     */
    private $linkBuilder;

    public function __construct(FactoryInterface $factory, LinkBuilderInterface $linkBuilder)
    {
        $this->factory     = $factory;
        $this->linkBuilder = $linkBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function create($data, $options = array())
    {
        $resourceDefinition = $this->factory->getResourceDefinition($data);
        $accessor           = PropertyAccess::getPropertyAccessor();

        $links = array();
        foreach ($resourceDefinition->getLinks() as $linkDefinition) {
            $links[] = $this->linkBuilder->createFromDefinition($linkDefinition, $data);

            // walk over the object properties to add links
            if (!empty($options['objectProperties'])) {
                foreach ($options['objectProperties'] as $property => $properties) {
                    $subOptions = array();
                    if (is_numeric($property)) {
                        $property = $properties;
                    } else {
                        // override objectProperties
                        $subOptions = $options;
                        $subOptions['objectProperties'] = $properties;
                    }

                    // get object
                    $obj = $accessor->getValue($data, $property);

                    // skip null values
                    if (null === $obj) {
                        continue;
                    }

                    // create resource and set object
                    $accessor->setValue($data, $property, $this->create($obj, $subOptions));
                }
            }
        }

        return new Resource($data, $links);
    }

    /**
     * {@inheritdoc}
     */
    public function createCollection($collection, $className, $options = array())
    {
        if (!is_array($collection) && !$collection instanceof \Traversable) {
            throw new \InvalidArgumentException(
                sprintf('Expected an array or a Traversable object, got: "%s".', is_object($collection) ? get_class($collection) : $collection)
            );
        }

        $collectionDefinition = $this->factory->getCollectionDefinition($className);

        $resources = array();
        foreach ($collection as $coll) {
            $resources[] = $this->create($coll, $options);
        }

        $links = array();
        foreach ($collectionDefinition->getLinks() as $linkDefinition) {
            $links[] = $this->linkBuilder->createFromDefinition($linkDefinition, $collection);
        }

        $accessor = PropertyAccess::getPropertyAccessor();

        // total
        if (null !== $total = $collectionDefinition->getTotal()) {
            $total = $accessor->getValue($collection, $total);
        }

        // limit
        if (null !== $limit = $collectionDefinition->getLimit()) {
            $limit = $accessor->getValue($collection, $limit);
        }

        // page
        if (null !== $page = $collectionDefinition->getPage()) {
            $page = $accessor->getValue($collection, $page);
        }

        return new Collection(
            $collectionDefinition->getRootName(),
            $resources,
            $links,
            $total,
            $page,
            $limit
        );
    }
}
