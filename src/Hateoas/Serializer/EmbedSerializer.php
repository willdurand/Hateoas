<?php

namespace Hateoas\Serializer;

use Hateoas\Model\Embedded;
use Hateoas\Serializer\Metadata\RelationPropertyMetadata;
use JMS\Serializer\SerializationContext;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class EmbedSerializer
{
    /**
     * @var SerializationContext
     */
    private $context;

    public function __construct(SerializationContext $context)
    {
        $this->context = $context;
    }

    public function serialize($data, Embedded $embedded)
    {
        // This setup the metadata stack so that it's legit to the DepthExclusionStrategy
        $this->context->pushPropertyMetadata(new RelationPropertyMetadata($embedded->getExclusion()));
        $serializedData = $this->context->accept($data);
        $this->context->popPropertyMetadata();

        return $serializedData;
    }

    public function getContext()
    {
        return $this->context;
    }
} 
