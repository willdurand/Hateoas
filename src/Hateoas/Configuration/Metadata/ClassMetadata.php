<?php

namespace Hateoas\Configuration\Metadata;

use Hateoas\Configuration\Relation;
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
     * {@inheritdoc}
     */
    public function getRelations()
    {
        return $this->relations;
    }

    /**
     * @param Relation $relation
     */
    public function addRelation(Relation $relation)
    {
        $this->relations[] = $relation;
    }

    public function serialize()
    {
        return serialize(array(
            $this->relations,
            parent::serialize(),
        ));
    }

    public function unserialize($str)
    {
        list(
            $this->relations,
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
    }
}
