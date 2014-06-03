<?php

namespace Hateoas\Serializer;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class SerializerRegistry
{
    const DEFAULT_NAME = 'default';

    private $defaultSerializerName;
    private $serializers;

    public function __construct($defaultSerializerName = null)
    {
        $this->setDefaultSerializerName($defaultSerializerName);
        $this->serializers = array();
    }

    public function setDefaultSerializerName($name)
    {
        $this->defaultSerializerName = $name ?: self::DEFAULT_NAME;
    }

    /**
     * @param string $name If null it will return the default serializer
     *
     * @return mixed
     */
    public function get($name)
    {
        if (null === $name) {
            $name = $this->defaultSerializerName;
        }

        if (!isset($this->serializers[$name])) {
            throw new \InvalidArgumentException(
                sprintf(
                    'The "%s" serializer is not set. Available serializers are: %s.',
                    $name,
                    join(', ', array_keys($this->serializers))
                )
            );
        }

        return $this->serializers[$name];
    }

    /**
     * @param string $name
     * @param mixed  $serializer
     */
    public function set($name, $serializer)
    {
        if (null === $name) {
            $name = self::DEFAULT_NAME;
        }

        $this->serializers[$name] = $serializer;
    }

    public function hasSerializers()
    {
        return count($this->serializers) > 0;
    }

    public function getSerializers()
    {
        return array_values($this->serializers);
    }
}
