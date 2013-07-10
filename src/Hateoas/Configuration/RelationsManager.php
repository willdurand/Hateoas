<?php

namespace Hateoas\Configuration;

use Hateoas\Util\ClassUtils;
use Metadata\MetadataFactoryInterface;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class RelationsManager
{
    /**
     * @var MetadataFactoryInterface
     */
    private $metadataFactory;

    /**
     * @var array fqcn => Relation[]
     */
    private $classesRelations = array();

    /**
     * @var array objectid => Relation[]
     */
    private $objectsRelations = array();

    public function __construct(MetadataFactoryInterface $metadataFactory = null)
    {
        $this->metadataFactory = $metadataFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getRelations($object)
    {
        $relations = array();

        if (null !== $this->metadataFactory) {
            $classMetadata = $this->metadataFactory->getMetadataForClass(get_class($object));
            if (null !== $classMetadata) {
                $relations = array_merge($relations, $classMetadata->getRelations());
            }
        }

        $class = strtolower(ClassUtils::getClass($object));
        if (isset($this->classesRelations[$class])) {
            $relations = array_merge($relations, $this->classesRelations[$class]);
        }

        $objectId = $this->getObjectId($object);
        if (isset($this->objectsRelations[$objectId])) {
            $relations = array_merge($relations, $this->objectsRelations[$objectId]);
        }

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
