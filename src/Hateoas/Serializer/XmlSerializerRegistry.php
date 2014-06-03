<?php

namespace Hateoas\Serializer;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class XmlSerializerRegistry
{
    /**
     * @var SerializerRegistry
     */
    private $registry;

    public function __construct($defaultJsonSerializerName = null)
    {
        $this->registry = new SerializerRegistry($defaultJsonSerializerName);
    }

    public function setDefaultSerializerName($name)
    {
        $this->registry->setDefaultSerializerName($name);
    }

    /**
     * @param  string|null            $name
     * @return XmlSerializerInterface
     */
    public function get($name = null)
    {
        return $this->registry->get($name);
    }

    /**
     * @param string|null            $name
     * @param XmlSerializerInterface $xmlSerializer
     */
    public function set($name, XmlSerializerInterface $xmlSerializer)
    {
        $this->registry->set($name, $xmlSerializer);
    }

    public function hasSerializers()
    {
        return $this->registry->hasSerializers();
    }

    /**
     * @return XmlSerializerInterface[]
     */
    public function getSerializers()
    {
        return $this->registry->getSerializers();
    }
}
