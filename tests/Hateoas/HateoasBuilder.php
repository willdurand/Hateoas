<?php

namespace tests\Hateoas;

use tests\Test;
use Hateoas\HateoasBuilder as TestedHateoasBuilder;

class HateoasBuilder extends Test
{
    public function test()
    {
        $hateoasBuilder = new TestedHateoasBuilder();
        $serializerBuilder = $hateoasBuilder->configureSerializerBuilder();

        $this
            ->object($serializerBuilder)
                ->isInstanceOf('JMS\Serializer\SerializerBuilder')
        ;
    }
}
