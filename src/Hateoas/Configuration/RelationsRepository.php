<?php

namespace Hateoas\Configuration;

use Hateoas\Configuration\Provider\RelationProviderInterface as RelationProviderProviderInterface;
use Metadata\MetadataFactoryInterface;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class RelationsRepository
{
    /**
     * @var MetadataFactoryInterface
     */
    private $metadataFactory;

    /**
     * @var RelationProviderProvider
     */
    private $relationProvider;

    /**
     * @param MetadataFactoryInterface          $metadataFactory
     * @param RelationProviderProviderInterface $relationProvider
     */
    public function __construct(MetadataFactoryInterface $metadataFactory, RelationProviderProviderInterface $relationProvider)
    {
        $this->metadataFactory  = $metadataFactory;
        $this->relationProvider = $relationProvider;
    }

    /**
     * @param object $object
     *
     * @return Relation[]
     */
    public function getRelations($object)
    {
        $relations = array();

        if (null !== $classMetadata = $this->metadataFactory->getMetadataForClass(get_class($object))) {
            $relations = array_merge($relations, $classMetadata->getRelations());
        }

        $relations = array_merge($relations, $this->relationProvider->getRelations($object));

        return $relations;
    }
}
