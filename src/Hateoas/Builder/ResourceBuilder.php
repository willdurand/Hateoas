<?php

namespace Hateoas\Builder;

use Hateoas\Collection;
use Hateoas\Resource;
use Hateoas\Factory\FactoryInterface;
use Hateoas\Builder\LinkBuilderInterface;
use Symfony\Component\Form\Util\PropertyPath;

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
                    $propertyPath = new PropertyPath($property);
                    $obj = $propertyPath->getValue($data);

                    // skip null values
                    if (null === $obj) {
                        continue;
                    }

                    // create resource and set object
                    $propertyPath->setValue($data, $this->create($obj, $subOptions));
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

        // total
        if (null !== $total = $collectionDefinition->getTotal()) {
            $propertyPath = new PropertyPath($total);
            $total = $propertyPath->getValue($collection);
        }

        // limit
        if (null !== $limit = $collectionDefinition->getLimit()) {
            $propertyPath = new PropertyPath($limit);
            $limit = $propertyPath->getValue($collection);
        }

        // page
        if (null !== $page = $collectionDefinition->getPage()) {
            $propertyPath = new PropertyPath($page);
            $page = $propertyPath->getValue($collection);
        }

        return new Collection(
            $resources,
            $links,
            $total,
            $page,
            $limit
        );
    }
}
