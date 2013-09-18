<?php

namespace Hateoas\Tests\Builder;

use Hateoas\Factory\Config\ArrayConfig;
use Hateoas\Factory\Factory;
use Hateoas\Builder\ResourceBuilder;
use Hateoas\Link;
use Hateoas\Tests\TestCase;
use Hateoas\Tests\Fixtures\DataClass1;
use Hateoas\Tests\Fixtures\DataClass2;

class ResourceBuilderTest extends TestCase
{
    public function testCreate()
    {
        $definitions = array(
            'Hateoas\Tests\Fixtures\DataClass2' => array(
                'links' => array(
                    array('rel' => 'foo', 'type' => 'bar'),
                ),
                'embeds' => array(
                    array('name' => 'dummy', 'accessor' => 'dummyClass')
                ),
            ),
            'Hateoas\Tests\Fixtures\DummyClass' => array(
                'links' => array(
                    array('rel' => 'dummy-rel', 'type' => 'dummy-type'),
                )
            )
        );

        $factory = new Factory(new ArrayConfig($definitions));
        $builder = new ResourceBuilder(
            $factory,
            $this->getLinkBuilderMock($this->exactly(4))
        );

        $resource = $builder->create(new DataClass2('test', new DataClass2('test2')), array('objectProperties' => array('child' => null)));

        $this->assertInstanceOf('Hateoas\Resource', $resource);
        $this->assertInstanceOf('Hateoas\Tests\Fixtures\DataClass2', $resource->getData());
        $this->assertInstanceOf('Hateoas\Tests\Fixtures\DataClass2', $resource->getData()->child->getData());

        // check links
        $this->assertCount(1, $resource->getLinks());
        $links = $resource->getLinks();

        $this->assertEquals('foo', $links[0]->getRel());
        $this->assertEquals('bar', $links[0]->getType());

        // check child links
        $this->assertCount(1, $resource->getData()->child->getLinks());
        $links = $resource->getData()->child->getLinks();

        $this->assertEquals('foo', $links[0]->getRel());
        $this->assertEquals('bar', $links[0]->getType());

        // check embed
        $embeds = $resource->getEmbeds();
        $this->assertCount(1, $embeds);
        $first = reset($embeds);

        $this->assertInstanceOf('Hateoas\Resource', $first);
        $this->assertInstanceOf('Hateoas\Tests\Fixtures\DummyClass', $first->getData());
        $this->assertCount(1, $first->getLinks());
        $links = $first->getLinks();
        $this->assertEquals('dummy-rel', $links[0]->getRel());
        $this->assertEquals('dummy-type', $links[0]->getType());
    }

    public function testCreateCollection()
    {
        $definitions = array(
            'Hateoas\Tests\Fixtures\DataClass1' => array(
                'links' => array(
                    array('rel' => 'test', 'type' => 'test'),
                ),
            ),
        );

        $collDefinitions = array(
            'Hateoas\Tests\Fixtures\DataClass1' => array(
                'links' => array(
                    array('rel' => 'foo', 'type' => 'bar'),
                    array('rel' => 'toto', 'type' => 'titi'),
                ),
            ),
        );

        $factory = new Factory(new ArrayConfig($definitions, $collDefinitions));
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
    
    public function testCreateCollectionWithLinkOverwrite()
    {
        $definitions = array(
            'Hateoas\Tests\Fixtures\DataClass1' => array(
                'links' => array(
                    array('rel' => 'test', 'type' => 'test'),
                ),
            ),
        );

        $collDefinitions = array(
            'Hateoas\Tests\Fixtures\DataClass1' => array(
                'links' => array(
                    array('rel' => 'foo', 'type' => 'bar'),
                    array('rel' => 'toto', 'type' => 'titi'),
                ),
            ),
        );

        $factory = new Factory(new ArrayConfig($definitions, $collDefinitions));
        $builder = new ResourceBuilder(
            $factory,
            $this->getLinkBuilderMock2($this->exactly(4))
        );

        $collection = $builder->createCollection(
            array(
                new DataClass1('test'),
                new DataClass1('baz')
            ),
            'Hateoas\Tests\Fixtures\DataClass1',
            array(),
            array(array('rel' => 'foo', 'definition' => array('rel' => 'foo', 'type' => 'bar2'), 'data' => new DataClass1('overridedLink')))
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
        $this->assertEquals('bar2', $links[0]->getType());
        $this->assertEquals('foo/overridedLink', $links[0]->getHref());

        $this->assertEquals('toto', $links[1]->getRel());
        $this->assertEquals('titi', $links[1]->getType());
        $this->assertEquals('toto', $links[1]->getHref());
    }

    public function testCreateCollectionWithLinkOverwriteWithoutData()
    {
        $definitions = array(
            'Hateoas\Tests\Fixtures\DataClass1' => array(
                'links' => array(
                    array('rel' => 'test', 'type' => 'test'),
                ),
            ),
        );

        $collDefinitions = array(
            'Hateoas\Tests\Fixtures\DataClass1' => array(
                'links' => array(
                    array('rel' => 'foo', 'type' => 'bar'),
                    array('rel' => 'toto', 'type' => 'titi'),
                ),
            ),
        );

        $factory = new Factory(new ArrayConfig($definitions, $collDefinitions));
        $builder = new ResourceBuilder(
            $factory,
            $this->getLinkBuilderMock2($this->exactly(4))
        );

        $collection = $builder->createCollection(
            array(
                new DataClass1('test'),
                new DataClass1('baz')
            ),
            'Hateoas\Tests\Fixtures\DataClass1',
            array(),
            array(array('rel' => 'foo', 'definition' => array('rel' => 'foo', 'type' => 'bar2')))
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
        $this->assertEquals('bar2', $links[0]->getType());
        $this->assertEquals('foo', $links[0]->getHref());

        $this->assertEquals('toto', $links[1]->getRel());
        $this->assertEquals('titi', $links[1]->getType());
        $this->assertEquals('toto', $links[1]->getHref());
    }
    
    public function testCreateCollectionWithArrayObject()
    {
        $definitions = array(
            'Hateoas\Tests\Fixtures\DataClass1' => array(
                'links' => array(
                    array('rel' => 'test', 'type' => 'test'),
                ),
            ),
        );

        $collDefinitions = array(
            'Hateoas\Tests\Fixtures\DataClass1' => array(
                'links' => array(
                    array('rel' => 'foo', 'type' => 'bar'),
                    array('rel' => 'toto', 'type' => 'titi'),
                ),
                'attributes' => array(
                    'total' => 'count()',
                ),
            ),
        );

        $factory = new Factory(new ArrayConfig($definitions, $collDefinitions));
        $builder = new ResourceBuilder(
            $factory,
            $this->getLinkBuilderMock($this->exactly(5))
        );

        $coll = new Collection();
        $coll->append(new DataClass1('test'));
        $coll->append(new DataClass1('fooo'));
        $coll->append(new DataClass1('barr'));

        $collection = $builder->createCollection($coll, 'Hateoas\Tests\Fixtures\DataClass1');
        $this->assertEquals(3, $collection->getTotal());
    }

    public function testCreateCollectionWithArray()
    {
        $definitions = array(
            'Hateoas\Tests\Fixtures\DataClass1' => array(
                'links' => array(
                    array('rel' => 'test', 'type' => 'test'),
                ),
            ),
        );

        $collDefinitions = array(
            'Hateoas\Tests\Fixtures\DataClass1' => array(
                'links' => array(
                    array('rel' => 'foo', 'type' => 'bar'),
                    array('rel' => 'toto', 'type' => 'titi'),
                ),
                'attributes' => array(
                    'total' => 'count()',
                ),
            ),
        );

        $factory = new Factory(new ArrayConfig($definitions, $collDefinitions));
        $builder = new ResourceBuilder(
            $factory,
            $this->getLinkBuilderMock($this->exactly(4))
        );

        $coll = array(
            new DataClass1('foo'),
            new DataClass1('bar'),
        );

        $collection = $builder->createCollection($coll, 'Hateoas\Tests\Fixtures\DataClass1');
        $this->assertEquals(2, $collection->getTotal());
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
    
    protected function getLinkBuilderMock2($expected)
    {
        $mock = $this->getMock('Hateoas\Builder\LinkBuilderInterface');
        $mock
            ->expects($expected)
            ->method('createFromDefinition')
            ->will($this->returnCallback(function($definition, $data){
	      $url = $definition->getRel();
	      
	      if($data && is_object($data)){
		$url = $url.'/'.$data->content;
	      }
	      
	      return new Link($url, $definition->getRel(), $definition->getType());
            }));

        return $mock;
    }
}

class Collection extends \ArrayObject {}
