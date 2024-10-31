<?php

declare(strict_types=1);

namespace Hateoas\Tests\Fixtures\Attribute;

use JMS\Serializer\Annotation as Serializer;

#[Serializer\XmlRoot('smartphone')]
class Smartphone
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
