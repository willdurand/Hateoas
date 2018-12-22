<?php

declare(strict_types=1);

namespace Hateoas\Tests\Representation;

use Hateoas\Representation\VndErrorRepresentation;

class VndErrorRepresentationTest extends RepresentationTestCase
{
    public function testSerialize()
    {
        $error = new VndErrorRepresentation(
            'Validation failed',
            42,
            'http://help/',
            'http://desc/'
        );

        $this->assertSame(
            <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<resource logref="42">
  <message><![CDATA[Validation failed]]></message>
  <link rel="help" href="http://help/"/>
  <link rel="describes" href="http://desc/"/>
</resource>

XML
            ,
            $this->hateoas->serialize($error, 'xml')
        );

        $this->assertSame(
            <<<JSON
{
    "message": "Validation failed",
    "logref": 42,
    "_links": {
        "help": {
            "href": "http:\/\/help\/"
        },
        "describes": {
            "href": "http:\/\/desc\/"
        }
    }
}
JSON
            ,
            $this->json($this->halHateoas->serialize($error, 'json'))
        );
    }
}
