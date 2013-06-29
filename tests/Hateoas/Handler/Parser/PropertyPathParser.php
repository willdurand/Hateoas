<?php

namespace tests\Hateoas\Handler\Parser;

use tests\Test;
use Hateoas\Handler\Parser\PropertyPathParser as TestedPropertyPathParser;

class PropertyPathParser extends Test
{
    public function testGetPropertyPath()
    {
        $handler = new TestedPropertyPathParser();

        $this
            ->string($handler->getPropertyPath('@this.id'))
                ->isEqualTo('id')
        ;
    }
}
