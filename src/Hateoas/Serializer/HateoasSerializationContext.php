<?php

namespace Hateoas\Serializer;

use JMS\Serializer\SerializationContext;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class HateoasSerializationContext extends SerializationContext
{
    private $xmlSerializerName = null;
    private $jsonSerializerName = null;

    public static function create()
    {
        return new static();
    }

    public function setXmlSerializerName($xmlSerializerName)
    {
        $this->xmlSerializerName = $xmlSerializerName;

        return $this;
    }

    public function getXmlSerializerName()
    {
        return $this->xmlSerializerName;
    }

    public function setJsonSerializerName($jsonSerializerName)
    {
        $this->jsonSerializerName = $jsonSerializerName;

        return $this;
    }

    public function getJsonSerializerName()
    {
        return $this->jsonSerializerName;
    }
}
