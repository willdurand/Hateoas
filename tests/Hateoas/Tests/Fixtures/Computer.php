<?php

namespace Hateoas\Tests\Fixtures;

use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\XmlRoot("computer")
 */
class Computer
{
    private $name;

    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }
}
