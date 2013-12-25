<?php

namespace Hateoas\Tests;

use Hateoas\HateoasBuilder;
use Hateoas\UrlGenerator\CallableUrlGenerator;
use JMS\Serializer\SerializationContext;
use Hateoas\Tests\Fixtures\AdrienBrault;
use Hateoas\Tests\Fixtures\WithAlternativeRouter;
use Hateoas\Tests\TestCase;

/**
 * Contains functional tests
 */
class HateoasBuilderTest extends TestCase
{
    public function testBuild()
    {
        $hateoasBuilder = new HateoasBuilder();
        $hateoas = $hateoasBuilder->build();

        $this
            ->object($hateoas)
                ->isInstanceOf('Hateoas\Hateoas')
        ;
    }

    public function testSerializeAdrienBraultWithExclusion()
    {
        $hateoas = HateoasBuilder::buildHateoas();

        $adrienBrault     = new AdrienBrault();
        $fakeAdrienBrault = new AdrienBrault();
        $fakeAdrienBrault->firstName = 'John';
        $fakeAdrienBrault->lastName = 'Smith';

        $context  = SerializationContext::create()->setGroups(array('simple'));
        $context2 = clone $context;

        $this
            ->string($hateoas->serialize($adrienBrault, 'xml', $context))
                ->isEqualTo(<<<XML
<?xml version="1.0" encoding="UTF-8"?>
<result>
  <first_name><![CDATA[Adrien]]></first_name>
  <last_name><![CDATA[Brault]]></last_name>
  <link rel="self" href="http://adrienbrault.fr"/>
  <link rel="computer" href="http://www.apple.com/macbook-pro/"/>
</result>

XML
                )
            ->string($hateoas->serialize($fakeAdrienBrault, 'xml', $context2))
                ->isEqualTo(<<<XML
<?xml version="1.0" encoding="UTF-8"?>
<result>
  <first_name><![CDATA[John]]></first_name>
  <last_name><![CDATA[Smith]]></last_name>
  <link rel="computer" href="http://www.apple.com/macbook-pro/"/>
</result>

XML
                )
        ;
    }

    public function testAlternativeUrlGenerator()
    {
        $brokenUrlGenerator = new CallableUrlGenerator(function ($name, $parameters) {
            return $name . '?' . http_build_query($parameters);
        });

        $hateoas = HateoasBuilder::create()
            ->setUrlGenerator('my_generator', $brokenUrlGenerator)
            ->build()
        ;

        $this
            ->string($hateoas->serialize(new WithAlternativeRouter(), 'xml'))
            ->isEqualTo(<<<XML
<?xml version="1.0" encoding="UTF-8"?>
<result>
  <link rel="search" href="/search?query=hello"/>
</result>

XML
            )
        ;
    }
}
