<?php

declare(strict_types=1);

namespace Hateoas;

use Hateoas\Helper\LinkHelper;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;

class Hateoas implements SerializerInterface
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var LinkHelper
     */
    private $linkHelper;

    public function __construct(SerializerInterface $serializer, LinkHelper $linkHelper)
    {
        $this->serializer = $serializer;
        $this->linkHelper = $linkHelper;
    }

    /**
     * {@inheritDoc}
     */
    public function serialize($data, string $format, ?SerializationContext $context = null, ?string $type = null): string
    {
        return $this->serializer->serialize($data, $format, $context, $type);
    }

    /**
     * {@inheritDoc}
     */
    public function deserialize(string $data, string $type, string $format, ?DeserializationContext $context = null)
    {
        return $this->serializer->deserialize($data, $type, $format, $context);
    }

    public function getSerializer(): SerializerInterface
    {
        return $this->serializer;
    }

    public function getLinkHelper(): LinkHelper
    {
        return $this->linkHelper;
    }
}
