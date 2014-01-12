<?php

namespace Hateoas\Tests\Representation;

use Hateoas\Configuration\Relation;
use Hateoas\Representation\VndErrorRepresentation;

class VndErrorRepresentationTest extends RepresentationTestCase
{
    public function testSerialize()
    {
        $error = new VndErrorRepresentation(
            'Validation failed',
            42,
            new Relation('help', 'http://.../', null, array('title' => 'Error Information')),
            new Relation('describes', 'http://.../', null, array('title' => 'Error Description'))
        );

        $this
            ->string($this->hateoas->serialize($error, 'xml'))
            ->isEqualTo(<<<XML
<?xml version="1.0" encoding="UTF-8"?>
<resource logref="42">
  <message><![CDATA[Validation failed]]></message>
  <link rel="help" href="http://.../" title="Error Information"/>
  <link rel="describes" href="http://.../" title="Error Description"/>
</resource>

XML
            );

        $this
            ->json($this->halHateoas->serialize($error, 'json'))
            ->isEqualTo(<<<JSON
{
    "message": "Validation failed",
    "logref": 42,
    "_links": {
        "help": {
            "href": "http:\/\/...\/",
            "title": "Error Information"
        },
        "describes": {
            "href": "http:\/\/...\/",
            "title": "Error Description"
        }
    }
}
JSON
            );
    }
}
