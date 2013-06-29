<?php

namespace Hateoas\Configuration;

use Metadata\MetadataFactory;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class RelationsManager implements RelationsManagerInterface
{
    private $metadataFactory;

    public function __construct(MetadataFactory $metadataFactory)
    {
        $this->metadataFactory = $metadataFactory;
    }

    /**
     * @param $object
     * @return Relation[]
     */
    public function getRelations($object)
    {
        $classMetadata = $this->metadataFactory->getMetadataForClass(get_class($object));

        return $classMetadata->getRelations();
    }

    /**
     * @param $object
     * @param Relation $relation
     * @return void
     */
    public function addRelation($object, Relation $relation)
    {

    }

    /**
     * @param $class
     * @param Relation $relation
     * @return void
     */
    public function addClassRelation($class, Relation $relation)
    {

    }
}
