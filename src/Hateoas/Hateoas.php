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
     * @deprecated You should use the `getLinkHelper()` method to get the helper instead of
     *             relying on this proxy method. This method will be removed as of 2.2.0.
     *
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
        return $this->getLinkHelper()->getLinkHref($object, $rel, $absolute);
    }

    /**
     * @return LinkHelper
     */
    public function getLinkHelper()
    {
        return $this->linkHelper;
    }
}
