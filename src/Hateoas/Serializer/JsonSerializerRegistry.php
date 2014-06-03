<?php

namespace Hateoas\Serializer;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class JsonSerializerRegistry
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
     * @param  string|null             $name
     * @return JsonSerializerInterface
     */
    public function get($name = null)
    {
        return $this->registry->get($name);
    }

    /**
     * @param string|null             $name
     * @param JsonSerializerInterface $jsonSerializer
     */
    public function set($name, JsonSerializerInterface $jsonSerializer)
    {
        $this->registry->set($name, $jsonSerializer);
    }

    /**
     * @return boolean
     */
    public function hasSerializers()
    {
        return $this->registry->hasSerializers();
    }

    /**
     * @return JsonSerializerInterface[]
     */
    public function getSerializers()
    {
        return $this->registry->getSerializers();
    }
}
