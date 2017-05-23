<?php

namespace Hateoas\Representation;

use JMS\Serializer\Annotation as Serializer;
use Hateoas\Configuration\Metadata\ClassMetadataInterface;

/**
 * Note that this will not provide the same result as the FSCHateoasBundle for xml.
 *
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class FSCCollectionRepresentation extends CollectionRepresentation
{
    /**
     * @Serializer\VirtualProperty
     * @Serializer\SerializedName("results")
     */
    public function getResult()
    {
        return $this->getResources();
    }

    public function getProviderRelations($object, ClassMetadataInterface $classMetadata)
    {
        return $this->getRelations();
    }
}
