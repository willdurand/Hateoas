<?php

namespace tests\Hateoas\Handler;

use tests\TestCase;
use Hateoas\Handler\PropertyPathHandler as TestedPropertyPathHandler;

class PropertyPathHandler extends TestCase
{
    public function testTransform()
    {
        $handler = new TestedPropertyPathHandler();
        $data = (object) array(
            'id' => 42,
        );

        $this
            ->integer($handler->transform('id', $data))
                ->isEqualTo(42)
        ;
    }
}
