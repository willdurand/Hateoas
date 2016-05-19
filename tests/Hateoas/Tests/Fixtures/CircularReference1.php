<?php

namespace Hateoas\Tests\Fixtures;

use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\ExclusionPolicy("all")
 *
 * @Hateoas\Relation("reference2", embedded="expr(object.getReference2())")
 */
class CircularReference1
{
    /**
     * @Serializer\Expose
     */
    private $name = 'reference1';

    private $reference2;

    public function setReference2($reference2)
    {
        $this->reference2 = $reference2;
    }

    public function getReference2()
    {
        return $this->reference2;
    }
}
