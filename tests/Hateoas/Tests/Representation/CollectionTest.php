<?php

namespace Hateoas\Tests\Representation;

use Hateoas\Representation\Collection;
use Hateoas\HateoasBuilder;
use Hateoas\Tests\TestCase;

class CollectionTest extends TestCase
{
    public function test()
    {
        $hateoas = HateoasBuilder::buildHateoas();
        $halHateoas = HateoasBuilder::create()->addXmlHalSerializer()->build();

        $collection = new Collection(
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
