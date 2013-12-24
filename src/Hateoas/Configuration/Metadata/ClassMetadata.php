<?php

namespace Hateoas\Configuration\Metadata;

use Hateoas\Configuration\Relation;
use Hateoas\Configuration\RelationProvider;
use Metadata\MergeableClassMetadata;
use Metadata\MergeableInterface;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class ClassMetadata extends MergeableClassMetadata implements ClassMetadataInterface
{
    /**
     * @var Relation[]
     */
    private $relations = array();

    /**
     * @var RelationProvider[]
     */
    private $relationProviders = array();

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritDoc}
     */
    public function getRelations()
    {
        return $this->relations;
    }

    /**
     * {@inheritDoc}
     */
    public function getRelationProviders()
    {
        return $this->relationProviders;
    }

    /**
     * {@inheritDoc}
     */
    public function addRelation(Relation $relation)
    {
        $this->relations[] = $relation;
    }

    /**
     * {@inheritDoc}
     */
    public function addRelationProvider(RelationProvider $relationProvider)
    {
        $this->relationProviders[] = $relationProvider;
    }

    /**
     * {@inheritDoc}
     */
    public function merge(MergeableInterface $object)
    {
        if (!$object instanceof self) {
            throw new \InvalidArgumentException(sprintf('Object must be an instance of %s.', __CLASS__));
        }

        parent::merge($object);

        $this->relations         = array_merge($this->relations, $object->getRelations());
        $this->relationProviders = array_merge($this->relationProviders, $object->getRelationProviders());
    }

    /**
     * {@inheritDoc}
     */
    public function serialize()
    {
        return serialize(array(
            $this->relations,
            $this->relationProviders,
            parent::serialize(),
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function unserialize($str)
    {
        list(
            $this->relations,
            $this->relationProviders,
            $parentStr
        ) = unserialize($str);

        parent::unserialize($parentStr);
    }
}
