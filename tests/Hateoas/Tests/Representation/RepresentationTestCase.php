<?php

namespace Hateoas\Tests\Representation;

use Hateoas\HateoasBuilder;
use Hateoas\Serializer\XmlHalSerializer;
use Hateoas\Tests\TestCase;
use Hateoas\UrlGenerator\CallableUrlGenerator;

abstract class RepresentationTestCase extends TestCase
{
    protected $hateoas;

    protected $halHateoas;

    private $queryStringUrlGenerator;

    protected function setUp()
    {
        parent::setUp();

        $this->queryStringUrlGenerator = new CallableUrlGenerator(function ($route, array $parameters, $absolute) {
            return ($absolute ? 'http://example.com' : '') . $route . '?' . http_build_query($parameters);
        });

        $this->hateoas = HateoasBuilder::create()
            ->setUrlGenerator(null, $this->queryStringUrlGenerator)
            ->build()
        ;

        $this->halHateoas = HateoasBuilder::create()
            ->setUrlGenerator(null, $this->queryStringUrlGenerator)
            ->setXmlSerializer(new XmlHalSerializer())
            ->build()
        ;
    }
}
