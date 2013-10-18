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
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getRelations()
    {
        return $this->relations;
    }

    /**
     * {@inheritdoc}
     */
    public function getRelationProviders()
    {
        return $this->relationProviders;
    }

    /**
     * @param Relation $relation
     */
    public function addRelation(Relation $relation)
    {
        $this->relations[] = $relation;
    }

    public function addRelationProvider(RelationProvider $relationProvider)
    {
        $this->relationProviders[] = $relationProvider;
    }

    public function serialize()
    {
        return serialize(array(
            $this->relations,
            $this->relationProviders,
            parent::serialize(),
        ));
    }

    public function unserialize($str)
    {
        list(
            $this->relations,
            $this->relationProviders,
            $parentStr
        ) = unserialize($str);

        parent::unserialize($parentStr);
    }

    /**
     * {@inheritdoc}
     */
    public function merge(MergeableInterface $object)
    {
        if (!$object instanceof self) {
            throw new \InvalidArgumentException(sprintf('Object must be an instance of %s.', __CLASS__));
        }

        parent::merge($object);

        $this->relations = array_merge($this->relations, $object->getRelations());
        $this->relationProviders = array_merge($this->relationProviders, $object->getRelationProviders());
    }
}
