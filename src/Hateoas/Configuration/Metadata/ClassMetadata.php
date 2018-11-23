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
    public function addRelation(Relation $relation)
    {
        $this->relations[] = $relation;
    }

    /**
     * {@inheritDoc}
     */
    public function merge(MergeableInterface $object): void
    {
        if (!$object instanceof self) {
            throw new \InvalidArgumentException(sprintf('Object must be an instance of %s.', __CLASS__));
        }

        parent::merge($object);

        $this->relations         = array_merge($this->relations, $object->getRelations());
    }

    /**
     * {@inheritDoc}
     */
    public function serialize()
    {
        return serialize(array(
            $this->relations,
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
            $parentStr
        ) = unserialize($str);

        parent::unserialize($parentStr);
    }
}
