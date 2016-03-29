<?php

namespace Hateoas\Tests\Fixtures;

use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\ExclusionPolicy("all")
 *
 * @Hateoas\Relation("reference1", embedded="expr(object.getReference1())")
 */
class CircularReference2
{
    /**
     * @Serializer\Expose
     */
    private $name = 'reference2';

    private $reference1;

    public function setReference1($reference1)
    {
        $this->reference1 = $reference1;
    }

    public function getReference1()
    {
        return $this->reference1;
    }
}
