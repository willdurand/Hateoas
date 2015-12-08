<?php

namespace Hateoas\Tests\Configuration\Metadata\Driver;

use Hateoas\Configuration\Metadata\ClassMetadata;
use Hateoas\Configuration\Metadata\Driver\ExtensionDriver;
use Hateoas\Configuration\Relation;
use Hateoas\Tests\TestCase;
use Prophecy\Argument;

class ExtensionDriverTest extends TestCase
{
    public function testDoesNothingIfNoExtension()
    {
        $reflectionClass = new \ReflectionClass(get_class($this));
        $classMetadata = new ClassMetadata(get_class($this));
        $loadMetadataForClassCallCount = 0;

        $delegateDriverProphecy = $this->prophesize('Metadata\Driver\DriverInterface');
        $delegateDriverProphecy
            ->loadMetadataForClass($reflectionClass)
            ->will(function () use (&$loadMetadataForClassCallCount, $classMetadata) {
                return $loadMetadataForClassCallCount++ < 1 ? $classMetadata : null;
            })
            ->shouldBeCalledTimes(2)
        ;
        $extensionDriver = new ExtensionDriver($delegateDriverProphecy->reveal(), array());

        $this->assertSame($classMetadata, $extensionDriver->loadMetadataForClass($reflectionClass));
        $this->assertNull($extensionDriver->loadMetadataForClass($reflectionClass));
    }

    public function testExtensions()
    {
        $reflectionClass = new \ReflectionClass(get_class($this));
        $classMetadata = new ClassMetadata(get_class($this));

        $extensions = array();
        for ($i = 0; $i < 2; $i++) {
            $extensionProphecy = $this->prophesize('Hateoas\Configuration\Metadata\ConfigurationExtensionInterface');
            $extensionProphecy
                ->decorate($classMetadata)
                ->shouldBeCalledTimes(1)
            ;
            $extensions[] = $extensionProphecy->reveal();
        }

        $delegateDriverProphecy = $this->prophesize('Metadata\Driver\DriverInterface');
        $delegateDriverProphecy
            ->loadMetadataForClass($reflectionClass)
            ->willReturn($classMetadata)
        ;

        $extensionDriver = new ExtensionDriver($delegateDriverProphecy->reveal(), $extensions);

        $this->assertSame($classMetadata, $extensionDriver->loadMetadataForClass($reflectionClass));
    }

    public function testDelegateReturnsNullAndNoExtensions()
    {
        $reflectionClass = new \ReflectionClass(get_class($this));

        $delegateDriverProphecy = $this->prophesize('Metadata\Driver\DriverInterface');
        $delegateDriverProphecy
            ->loadMetadataForClass($reflectionClass)
            ->willReturn(null)
        ;

        $extensionDriver = new ExtensionDriver($delegateDriverProphecy->reveal(), array());

        $this->assertNull($extensionDriver->loadMetadataForClass($reflectionClass));
    }

    public function testDelegateReturnsNullAndExtensionsDoNothing()
    {
        $reflectionClass = new \ReflectionClass(get_class($this));

        $extensionProphecy = $this->prophesize('Hateoas\Configuration\Metadata\ConfigurationExtensionInterface');
        $extensionProphecy
            ->decorate(Argument::type('Hateoas\Configuration\Metadata\ClassMetadataInterface'))
            ->shouldBeCalledTimes(1)
        ;

        $delegateDriverProphecy = $this->prophesize('Metadata\Driver\DriverInterface');
        $delegateDriverProphecy
            ->loadMetadataForClass($reflectionClass)
            ->willReturn(null)
        ;

        $extensionDriver = new ExtensionDriver($delegateDriverProphecy->reveal(), array($extensionProphecy->reveal()));

        $this->assertNull($extensionDriver->loadMetadataForClass($reflectionClass));
    }

    public function testDelegateReturnsNullAndExtensionsAddRelations()
    {
        $reflectionClass = new \ReflectionClass(get_class($this));

        $extensionProphecy = $this->prophesize('Hateoas\Configuration\Metadata\ConfigurationExtensionInterface');
        $extensionProphecy
            ->decorate(Argument::type('Hateoas\Configuration\Metadata\ClassMetadataInterface'))
            ->will(function ($args) {
                $args[0]->addRelation(new Relation('foo', 'bar'));
            })
            ->shouldBeCalledTimes(1)
        ;

        $delegateDriverProphecy = $this->prophesize('Metadata\Driver\DriverInterface');
        $delegateDriverProphecy
            ->loadMetadataForClass($reflectionClass)
            ->willReturn(null)
        ;

        $extensionDriver = new ExtensionDriver($delegateDriverProphecy->reveal(), array($extensionProphecy->reveal()));

        $this->assertInstanceOf(
            'Hateoas\Configuration\Metadata\ClassMetadataInterface',
            $extensionDriver->loadMetadataForClass($reflectionClass)
        );
    }
}
