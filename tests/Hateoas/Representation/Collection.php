<?php

namespace tests\Hateoas\Representation;

use Hateoas\Representation\Collection as TestedCollection;
use Hateoas\HateoasBuilder;
use tests\TestCase;

class Collection extends TestCase
{
    public function test()
    {
        $hateoas = HateoasBuilder::buildHateoas();
        $halHateoas = HateoasBuilder::create()->addXmlHalSerializer()->build();

        $collection = new TestedCollection(
            array(
                'Adrien',
                'William',
            ),
            'authors'
        );
        $collection->setXmlElementName('users');

        $this
            ->string($hateoas->serialize($collection, 'xml'))
                ->isEqualTo(
<<<XML
<?xml version="1.0" encoding="UTF-8"?>
<collection>
  <users rel="authors">
    <entry><![CDATA[Adrien]]></entry>
    <entry><![CDATA[William]]></entry>
  </users>
</collection>

XML
                )
            ->string($halHateoas->serialize($collection, 'xml'))
                ->isEqualTo(
<<<XML
<?xml version="1.0" encoding="UTF-8"?>
<collection>
  <resource rel="authors"><![CDATA[Adrien]]></resource>
  <resource rel="authors"><![CDATA[William]]></resource>
</collection>

XML
                )
            ->string($halHateoas->serialize($collection, 'json'))
                ->isEqualTo(
                    '{"_embedded":{"authors":["Adrien","William"]}}'
                )
        ;
    }
}
