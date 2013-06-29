<?php

namespace tests\Hateoas\Handler;

use tests\Test;
use Hateoas\Handler\PropertyPathHandler as TestedPropertyPathHandler;

class PropertyPathHandler extends Test
{
    public function testTransform()
    {
        $handler = new TestedPropertyPathHandler();
        $data = (object)array(
            'id' => 42,
        );

        $this
            ->integer($handler->transform('@this.id', $data))
                ->isEqualTo(42)
        ;
    }
}
