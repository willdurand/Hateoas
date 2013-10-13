<?php

namespace Hateoas\Tests\Configuration\Provider;

use Hateoas\Configuration\Relation;
use Hateoas\Configuration\RelationProvider as RelationProviderConfiguration;
use Hateoas\Configuration\Provider\RelationProvider;
use Hateoas\Tests\TestCase;

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
            ->getRelationProvider($this->arg->which('getName', 'getRelations'), $this->arg->any())
            ->willReturn(function () use ($relations1) {
                return $relations1;
            })
        ;
        $resolverProphecy
            ->getRelationProvider($this->arg->which('getName', 'Class:getRelations'), $this->arg->any())
            ->willReturn(function () use ($relations2) {
                return $relations2;
            })
        ;

        $relationProvider = new RelationProvider($metadataFactoryProphecy->reveal(), $resolverProphecy->reveal());

        $object = new \StdClass();

        $this
            ->array($relationProvider->getRelations($object))
                ->isEqualTo(array($relation1, $relation2))
        ;
    }
}
