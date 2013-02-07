<?php

namespace Hateoas\Tests\Fixtures;

use JMS\Serializer\Annotation\XmlRoot;

/**
 * @XmlRoot("data_class")
 */
class DataClass1
{
    public $content;

    public $child;

    public function __construct($content, $child = null)
    {
        $this->content = $content;
        $this->child   = $child;
    }
}
