<?php

namespace tests\Hateoas\Configuration\Provider;

use Hateoas\Configuration\Relation;
use Hateoas\Configuration\RelationProvider as RelationProviderConfiguration;
use Hateoas\Configuration\Provider\RelationProvider as TestedRelationProvider;
use tests\TestCase;

class RelationProvider extends TestCase
{
    public function test()
    {
        $relationProviders = array(
            new RelationProviderConfiguration('getRelations'),
            new RelationProviderConfiguration('Class:getRelations'),
        );
        $relations = array(
            new Relation('foo'),
            new Relation('bar'),
        );

        $metadataFactoryMock = new \mock\Metadata\MetadataFactoryInterface();
        $relationProviderProvider = new \mock\Hateoas\Configuration\Provider\RelationProviderProviderInterface();

        $metadataFactoryMock->getMockController()->getMetadataForClass = function () use ($relationProviders) {
            $classMetadata = new \mock\Hateoas\Configuration\Metadata\ClassMetadataInterface();
            $classMetadata->getMockController()->getRelationProviders = function () use ($relationProviders) {
                return $relationProviders;
            };

            return $classMetadata;
        };
        $relationProviderProvider->getMockController()->get = function (RelationProviderConfiguration $relationProvider, $object) use ($relations) {
            $relations = $relationProvider->getName() == 'getRelations'
                ? array($relations[0])
                : array($relations[1])
            ;

            return function () use ($relations) {
                return $relations;
            };
        };

        $relationProvider = new TestedRelationProvider($metadataFactoryMock, $relationProviderProvider);

        $object = new \StdClass();

        $this
            ->array($relationProvider->getRelations($object))
                ->isEqualTo(array($relations[0], $relations[1]))
        ;
    }
}
