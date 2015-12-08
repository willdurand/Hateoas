<?php

namespace Hateoas\Tests\Representation;

use Hateoas\HateoasBuilder;
use Hateoas\Serializer\XmlHalSerializer;
use Hateoas\Tests\TestCase;
use Hateoas\UrlGenerator\CallableUrlGenerator;

abstract class RepresentationTestCase extends TestCase
{
    /**
     * @var \Hateoas\Hateoas
     */
    protected $hateoas;

    /**
     * @var \Hateoas\Hateoas
     */
    protected $halHateoas;

    private $queryStringUrlGenerator;

    protected function setUp()
    {
        parent::setUp();

        $this->queryStringUrlGenerator = new CallableUrlGenerator(function ($route, array $parameters, $absolute) {
            if ('' !== $queryString = http_build_query($parameters)) {
                $queryString = '?' . $queryString;
            }

            return ($absolute ? 'http://example.com' : '') . $route . $queryString;
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
