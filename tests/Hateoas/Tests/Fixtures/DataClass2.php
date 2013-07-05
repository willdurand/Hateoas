<?php

namespace Hateoas\Tests\Fixtures;

use JMS\Serializer\Annotation\XmlRoot;

/**
 * @XmlRoot("data_class")
 */
class DataClass2
{
    public $content;

    public $child;

    public $dummyClass;

    public function __construct($content, $child = null)
    {
        $this->content = $content;
        $this->child = $child;
        $this->dummyClass = new DummyClass();
    }

    public function getDummyClass()
    {
        return $this->dummyClass;
    }
}
