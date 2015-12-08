<?php

namespace Hateoas\Tests\Configuration\Provider;

use Hateoas\Configuration\Relation;
use Hateoas\Configuration\RelationProvider as RelationProviderConfiguration;
use Hateoas\Configuration\Provider\RelationProvider;
use Hateoas\Tests\TestCase;
use Prophecy\Argument;

class RelationProviderTest extends TestCase
{
    public function test()
    {
        $relationProviders = array(
            new RelationProviderConfiguration('getRelations'),
            new RelationProviderConfiguration('Class:getRelations'),
        );
        $relations1 = array($relation1 = new Relation('foo'));
        $relations2 = array($relation2 = new Relation('bar'));

        $classMetadataProphecy = $this->prophesize('Hateoas\Configuration\Metadata\ClassMetadataInterface');
        $classMetadataProphecy
            ->getRelationProviders()
            ->willReturn($relationProviders)
        ;

        $metadataFactoryProphecy = $this->prophesize('Metadata\MetadataFactoryInterface');
        $metadataFactoryProphecy
            ->getMetadataForClass('stdClass')
            ->willReturn($classMetadataProphecy->reveal())
        ;

        $resolverProphecy = $this->prophesize('Hateoas\Configuration\Provider\Resolver\RelationProviderResolverInterface');
        $resolverProphecy
            ->getRelationProvider(Argument::which('getName', 'getRelations'), Argument::any())
            ->willReturn(function () use ($relations1) {
                return $relations1;
            })
        ;
        $resolverProphecy
            ->getRelationProvider(Argument::which('getName', 'Class:getRelations'), Argument::any())
            ->willReturn(function () use ($relations2) {
                return $relations2;
            })
        ;

        $relationProvider = new RelationProvider($metadataFactoryProphecy->reveal(), $resolverProphecy->reveal());

        $object = new \StdClass();

        $this->assertSame([$relation1, $relation2], $relationProvider->getRelations($object));
    }
}
