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
     * {@inheritdoc}
     */
    public function getRelations($object)
    {
        $classMetadata = $this->metadataFactory->getMetadataForClass(get_class($object));

        return $classMetadata->getRelations();
    }

    /**
     * {@inheritdoc}
     */
    public function addRelation($object, Relation $relation)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function addClassRelation($class, Relation $relation)
    {

    }
}
