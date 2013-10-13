<?php

namespace Hateoas\Configuration;

use Hateoas\Configuration\Provider\RelationProvider;
use Hateoas\Util\ClassUtils;
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
     * @var RelationProvider
     */
    private $relationProvider;

    /**
     * @var array fqcn => Relation[]
     */
    private $classesRelations = array();

    /**
     * @var array objectid => Relation[]
     */
    private $objectsRelations = array();

    /**
     * @param MetadataFactoryInterface $metadataFactory
     * @param RelationProvider         $relationProvider
     */
    public function __construct(
        MetadataFactoryInterface $metadataFactory,
        RelationProvider $relationProvider
    ) {
        $this->metadataFactory = $metadataFactory;
        $this->relationProvider = $relationProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getRelations($object)
    {
        $relations = array();

        if (null !== $classMetadata = $this->metadataFactory->getMetadataForClass(get_class($object))) {
            $relations = array_merge($relations, $classMetadata->getRelations());
        }

        $class = strtolower(ClassUtils::getClass($object));
        if (isset($this->classesRelations[$class])) {
            $relations = array_merge($relations, $this->classesRelations[$class]);
        }

        $objectId = $this->getObjectId($object);
        if (isset($this->objectsRelations[$objectId])) {
            $relations = array_merge($relations, $this->objectsRelations[$objectId]);
        }

        $relations = array_merge($relations, $this->relationProvider->getRelations($object));

        return $relations;
    }

    /**
     * {@inheritdoc}
     */
    public function addRelation($object, Relation $relation)
    {
        $this->objectsRelations[$this->getObjectId($object)][] = $relation;
    }

    /**
     * {@inheritdoc}
     */
    public function addClassRelation($class, Relation $relation)
    {
        $class = strtolower(ClassUtils::getRealClass($class));

        $this->classesRelations[$class][] = $relation;
    }

    private function getObjectId($object)
    {
        return spl_object_hash($object);
    }
}
