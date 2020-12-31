<?php

declare(strict_types=1);

namespace Hateoas\Configuration\Metadata;

use Hateoas\Configuration\Relation;
use Metadata\MergeableClassMetadata;
use Metadata\MergeableInterface;

class ClassMetadata extends MergeableClassMetadata implements ClassMetadataInterface
{
    /**
     * @var Relation[]
     */
    private $relations = [];

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * {@inheritDoc}
     */
    public function getRelations(): array
    {
        return $this->relations;
    }

    public function addRelation(Relation $relation): void
    {
        $this->relations[] = $relation;
    }

    public function merge(MergeableInterface $object): void
    {
        if (!$object instanceof self) {
            throw new \InvalidArgumentException(sprintf('Object must be an instance of %s.', self::class));
        }

        parent::merge($object);

        $this->relations = array_merge($this->relations, $object->getRelations());
    }

    /**
     * {@inheritDoc}
     */
    public function serialize()
    {
        return serialize([
            $this->relations,
            parent::serialize(),
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function unserialize($str)
    {
        [
            $this->relations,
            $parentStr,
        ] = unserialize($str);

        parent::unserialize($parentStr);
    }
}
