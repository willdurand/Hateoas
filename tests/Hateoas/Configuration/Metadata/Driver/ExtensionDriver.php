<?php

namespace tests\Hateoas\Configuration\Metadata\Driver;

use Hateoas\Configuration\Metadata\ClassMetadata;
use Hateoas\Configuration\Metadata\ClassMetadataInterface;
use Hateoas\Configuration\Metadata\Driver\ExtensionDriver as TestedExtensionDriver;
use Hateoas\Configuration\Relation;
use tests\TestCase;

class ExtensionDriver extends TestCase
{
    public function testDoesNothingIfNoExtension()
    {
        $delegateDriver = new \mock\Metadata\Driver\DriverInterface();
        $classMetadata = new ClassMetadata(get_class($this));
        $i = 0;
        $delegateDriver->getMockController()->loadMetadataForClass = function () use (&$i, $classMetadata) {
            return $i++ < 1 ? $classMetadata : null;
        };

        $extensionDriver = new TestedExtensionDriver($delegateDriver, array());
        $reflectionClass = new \ReflectionClass(get_class($this));

        $this
            ->variable($extensionDriver->loadMetadataForClass($reflectionClass))
                ->isEqualTo($classMetadata)
            ->variable($extensionDriver->loadMetadataForClass($reflectionClass))
                ->isNull()
            ->mock($delegateDriver)
                ->call('loadMetadataForClass')
                    ->withArguments($reflectionClass)
                    ->twice()
        ;
    }

    public function testExtensions()
    {
        $extensions = array(
            new \mock\Hateoas\Configuration\Metadata\ConfigurationExtensionInterface(),
            new \mock\Hateoas\Configuration\Metadata\ConfigurationExtensionInterface(),
        );
        $classMetadata = new ClassMetadata(get_class($this));
        $delegateDriver = new \mock\Metadata\Driver\DriverInterface();
        $delegateDriver->getMockController()->loadMetadataForClass = function () use ($classMetadata) {
            return $classMetadata;
        };

        $extensionDriver = new TestedExtensionDriver($delegateDriver, $extensions);
        $reflectionClass = new \ReflectionClass(get_class($this));

        $this
            ->variable($extensionDriver->loadMetadataForClass($reflectionClass))
                ->isIdenticalTo($classMetadata)
            ->mock($extensions[0])
                ->call('decorate')
                    ->withArguments($classMetadata)
                    ->once()
            ->mock($extensions[1])
                ->call('decorate')
                    ->withArguments($classMetadata)
                    ->once()
        ;
    }

    public function testDelegateReturnsNullAndNoExtensions()
    {
        $delegateDriver = new \mock\Metadata\Driver\DriverInterface();
        $extensionDriver = new TestedExtensionDriver($delegateDriver, array());
        $reflectionClass = new \ReflectionClass(get_class($this));

        $this
            ->variable($extensionDriver->loadMetadataForClass($reflectionClass))
                ->isNull()
        ;
    }

    public function testDelegateReturnsNullAndExtensionsDoNothing()
    {
        $extension = new \mock\Hateoas\Configuration\Metadata\ConfigurationExtensionInterface();
        $delegateDriver = new \mock\Metadata\Driver\DriverInterface();

        $extensionDriver = new TestedExtensionDriver($delegateDriver, array($extension));
        $reflectionClass = new \ReflectionClass(get_class($this));

        $this
            ->variable($extensionDriver->loadMetadataForClass($reflectionClass))
                ->isNull()
            ->mock($extension)
                ->call('decorate')
                    ->once()
        ;
    }

    public function testDelegateReturnsNullAndExtensionsAddRelations()
    {
        $extension = new \mock\Hateoas\Configuration\Metadata\ConfigurationExtensionInterface();
        $extension->getMockController()->decorate = function (ClassMetadataInterface $classMetadata) {
            $classMetadata->addRelation(new Relation('foo', 'bar'));
        };
        $delegateDriver = new \mock\Metadata\Driver\DriverInterface();

        $extensionDriver = new TestedExtensionDriver($delegateDriver, array($extension));
        $reflectionClass = new \ReflectionClass(get_class($this));

        $this
            ->object($extensionDriver->loadMetadataForClass($reflectionClass))
                ->isInstanceOf('Hateoas\Configuration\Metadata\ClassMetadataInterface')
            ->mock($extension)
                ->call('decorate')
                    ->once()
        ;
    }
}
