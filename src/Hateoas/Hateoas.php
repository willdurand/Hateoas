<?php

namespace Hateoas;

use Hateoas\Helper\LinkHelper;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
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

    /**
     * @param SerializerInterface $serializer
     * @param LinkHelper          $linkHelper
     */
    public function __construct(SerializerInterface $serializer, LinkHelper $linkHelper)
    {
        $this->serializer = $serializer;
        $this->linkHelper = $linkHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize($data, $format, SerializationContext $context = null)
    {
        return $this->serializer->serialize($data, $format, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function deserialize($data, $type, $format, DeserializationContext $context = null)
    {
        return $this->serializer->deserialize($data, $type, $format, $context);
    }

    /**
     * @return SerializerInterface
     */
    public function getSerializer()
    {
        return $this->serializer;
    }

    /**
     * Gets the 'href' value of an object's link, identified by its rel name.
     *
     * @param object  $object
     * @param string  $rel
     * @param boolean $absolute
     *
     * @return string|null
     */
    public function getLinkHref($object, $rel, $absolute = false)
    {
        return $this->linkHelper->getLinkHref($object, $rel, $absolute);
    }
}
