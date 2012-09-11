<?php

namespace Hateoas\Tests\Builder;

use Hateoas\Factory\Factory;
use Hateoas\Builder\ResourceBuilder;
use Hateoas\Tests\TestCase;
use Hateoas\Tests\Fixtures\DataClass1;

class ResourceBuilderTest extends TestCase
{
    public function testCreate()
    {
        $definitions = array(
            'Hateoas\Tests\Fixtures\DataClass1' => array(
                array('rel' => 'foo', 'type' => 'bar'),
            ),
        );

        $factory = new Factory($definitions);
        $builder = new ResourceBuilder(
            $factory,
            $this->getLinkBuilderMock($this->once())
        );

        $resource = $builder->create(new DataClass1('test'));

        $this->assertInstanceOf('Hateoas\Resource', $resource);
        $this->assertInstanceOf('Hateoas\Tests\Fixtures\DataClass1', $resource->getData());

        $this->assertCount(1, $resource->getLinks());
        $links = $resource->getLinks();

        $this->assertEquals('foo', $links[0]->getRel());
        $this->assertEquals('bar', $links[0]->getType());
    }

    public function testCreateCollection()
    {
        $definitions = array(
            'Hateoas\Tests\Fixtures\DataClass1' => array(
                array('rel' => 'test', 'type' => 'test'),
            ),
        );

        $collDefinitions = array(
            'Hateoas\Tests\Fixtures\DataClass1' => array(
                array('rel' => 'foo', 'type' => 'bar'),
                array('rel' => 'toto', 'type' => 'titi'),
            ),
        );

        $factory = new Factory($definitions, $collDefinitions);
        $builder = new ResourceBuilder(
            $factory,
            $this->getLinkBuilderMock($this->exactly(4))
        );

        $collection = $builder->createCollection(
            array(
                new DataClass1('test'),
                new DataClass1('baz')
            ),
            'Hateoas\Tests\Fixtures\DataClass1'
        );

        $this->assertInstanceOf('Hateoas\Collection', $collection);
        $this->assertCount(2, $collection->getResources());

        foreach ($collection->getResources() as $resource) {
            $this->assertInstanceOf('Hateoas\Resource', $resource);
            $this->assertInstanceOf('Hateoas\Tests\Fixtures\DataClass1', $resource->getData());

            $this->assertCount(1, $resource->getLinks());
            $links = $resource->getLinks();

            $this->assertEquals('test', $links[0]->getRel());
            $this->assertEquals('test', $links[0]->getType());
        }

        $this->assertCount(2, $collection->getLinks());
        $links = $collection->getLinks();

        $this->assertEquals('foo', $links[0]->getRel());
        $this->assertEquals('bar', $links[0]->getType());

        $this->assertEquals('toto', $links[1]->getRel());
        $this->assertEquals('titi', $links[1]->getType());
    }

    protected function getLinkBuilderMock($expected)
    {
        $mock = $this->getMock('Hateoas\Builder\LinkBuilderInterface');
        $mock
            ->expects($expected)
            ->method('createFromDefinition')
            ->will($this->returnArgument(0));

        return $mock;
    }
}
