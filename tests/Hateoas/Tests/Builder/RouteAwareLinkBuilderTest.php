<?php

namespace Hateoas\Tests\Builder;

use Hateoas\Builder\RouteAwareLinkBuilder;
use Hateoas\Factory\Definition\LinkDefinition;
use Hateoas\Factory\Definition\RouteLinkDefinition;
use Hateoas\Tests\TestCase;

class RouteAwareLinkBuilderTest extends TestCase
{
    public function testCreateFromDefinitionWithInvalidLinkDefinition()
    {
        $urlGeneratorMock = $this->getMock('Symfony\Component\Routing\Generator\UrlGeneratorInterface');
        $urlGeneratorMock->expects($this->never())->method('generate');

        $builder = new RouteAwareLinkBuilder($urlGeneratorMock);
        $result  = $builder->createFromDefinition(
            new LinkDefinition('Foo'),
            array()
        );

        $this->assertNull($result);
    }

    public function testCreateFromDefinition()
    {
        $urlGeneratorMock = $this->getMock('Symfony\Component\Routing\Generator\UrlGeneratorInterface', array('generate'));
        $urlGeneratorMock
            ->expects($this->once())
            ->method('generate')
            ->will($this->returnArgument(0));

        $builder = new RouteAwareLinkBuilder($urlGeneratorMock);
        $result  = $builder->createFromDefinition(
            new RouteLinkDefinition('foo.get', array(), 'rel'),
            array()
        );

        $this->assertInstanceOf('Hateoas\Link', $result);
        $this->assertEquals('foo.get', $result->getHref());
        $this->assertEquals('rel', $result->getRel());
    }

    public function testCreateFromDefinitionWithParameters()
    {
        $urlGeneratorMock = $this->getMock('Symfony\Component\Routing\Generator\UrlGeneratorInterface', array('generate'));
        $urlGeneratorMock
            ->expects($this->once())
            ->method('generate')
            ->will($this->returnArgument(1));

        $builder = new RouteAwareLinkBuilder($urlGeneratorMock);
        $result  = $builder->createFromDefinition(
            new RouteLinkDefinition('foo.get', array(
                array('id' => 'id'),
                'alone',
            ), 'rel'),
            new DummyClass()
        );

        $this->assertInstanceOf('Hateoas\Link', $result);
        $this->assertEquals('rel', $result->getRel());

        $parameters = $result->getHref(); // mock returns 'parameters' in this attribute
        $this->assertArrayHasKey('id', $parameters);
        $this->assertEquals(10, $parameters['id']);
        $this->assertArrayHasKey('alone', $parameters);
        $this->assertEquals('alone', $parameters['alone']);
    }
}

class DummyClass
{
    public $id    = 10;
    public $alone = 'alone';
}
