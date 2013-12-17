<?php

namespace Hateoas\Tests\Configuration\Metadata\Driver;

use Hateoas\Configuration\Relation;
use Hateoas\Configuration\RelationProvider;
use Metadata\Driver\DriverInterface;
use Hateoas\Tests\TestCase;

abstract class AbstractDriverTest extends TestCase
{
    /**
     * @return DriverInterface
     */
    abstract public function createDriver();

    public function testUser()
    {
        $driver = $this->createDriver();
        $class = new \ReflectionClass('Hateoas\Tests\Fixtures\User');
        $classMetadata = $driver->loadMetadataForClass($class);

        $this
            ->object($classMetadata)
                ->isInstanceOf('Hateoas\Configuration\Metadata\ClassMetadata')
        ;

        /** @var $relations Relation[] */
        $relations = $classMetadata->getRelations();

        $this->array($relations);
        foreach ($relations as $relation) {
            $this
                ->object($relation)
                    ->isInstanceOf('Hateoas\Configuration\Relation')
            ;
        }

        $i = 0;

        $relation = $relations[$i++];
        $this
            ->string($relation->getName())
                ->isEqualTo('self')
            ->string($relation->getHref())
                ->isEqualTo('http://hateoas.web/user/42')
            ->array($relation->getAttributes())
                ->isEqualTo(array(
                    'type' => 'application/json',
                ))
            ->variable($relation->getEmbedded())
                ->isNull()
            ->variable($relation->getExclusion())
                ->isNull()
        ;

        $relation = $relations[$i++];
        $this
            ->string($relation->getName())
                ->isEqualTo('foo')
            ->object($relation->getHref())
                ->isInstanceOf('Hateoas\Configuration\Route')
                ->and($route = $relation->getHref())
                    ->string($route->getName())
                        ->isEqualTo('user_get')
                    ->array($route->getParameters())
                        ->isEqualTo(array(
                            'id' => 'expr(object.getId())',
                        ))
                    ->boolean($route->isAbsolute())
                        ->isEqualTo(false)
            ->object($relation->getEmbedded())
                ->isInstanceOf('Hateoas\Configuration\Embedded')
                ->and($embedded = $relation->getEmbedded())
                    ->string($embedded->getContent())
                        ->isEqualTo('expr(object.getFoo())')
                    ->variable($embedded->getXmlElementName())
                        ->isNull()
            ->variable($relation->getExclusion())
                ->isNull()
        ;

        $relation = $relations[$i++];
        $this
            ->string($relation->getName())
                ->isEqualTo('bar')
            ->string($relation->getHref())
                ->isEqualTo('foo')
            ->object($relation->getEmbedded())
                ->isInstanceOf('Hateoas\Configuration\Embedded')
                ->and($embedded = $relation->getEmbedded())
                    ->variable($embedded->getContent())
                        ->isEqualTo('data')
                    ->string($embedded->getXmlElementName())
                        ->isEqualTo('barTag')
            ->variable($relation->getExclusion())
                ->isNull()
        ;

        $relation = $relations[$i++];
        $this
            ->string($relation->getName())
                ->isEqualTo('baz')
            ->object($relation->getHref())
                ->isInstanceOf('Hateoas\Configuration\Route')
                ->and($route = $relation->getHref())
                    ->string($route->getName())
                        ->isEqualTo('user_get')
                    ->array($route->getParameters())
                        ->isEqualTo(array(
                            'id' => 'expr(object.getId())',
                        ))
                    ->boolean($route->isAbsolute())
                        ->isEqualTo(true)
            ->variable($relation->getExclusion())
                ->isNull()
        ;

        $relation = $relations[$i++];
        $this
            ->string($relation->getName())
                ->isEqualTo('boom')
            ->object($relation->getHref())
                ->isInstanceOf('Hateoas\Configuration\Route')
                ->and($route = $relation->getHref())
                    ->string($route->getName())
                        ->isEqualTo('user_get')
                    ->array($route->getParameters())
                        ->isEqualTo(array(
                            'id' => 'expr(object.getId())',
                        ))
                    ->boolean($route->isAbsolute())
                        ->isEqualTo(false)
            ->variable($relation->getExclusion())
                ->isNull()
        ;

        $relation = $relations[$i++];
        $this
            ->string($relation->getName())
                ->isEqualTo('badaboom')
            ->variable($relation->getHref())
                ->isNull()
            ->object($relation->getEmbedded())
                ->isInstanceOf('Hateoas\Configuration\Embedded')
                ->and($embedded = $relation->getEmbedded())
                    ->string($embedded->getContent())
                        ->isEqualTo('expr(object.getFoo())')
            ->variable($relation->getExclusion())
                ->isNull()
        ;

        $relation = $relations[$i++];
        $this
            ->string($relation->getName())
                ->isEqualTo('hello')
            ->variable($relation->getHref())
                ->isEqualTo('/hello')
            ->object($relation->getExclusion())
                ->isInstanceOf('Hateoas\Configuration\Exclusion')
                ->and($exclusion = $relation->getExclusion())
                    ->array($exclusion->getGroups())
                        ->isEqualTo(array('group1', 'group2'))
                    ->float($exclusion->getSinceVersion())
                        ->isEqualTo(1.0)
                    ->float($exclusion->getUntilVersion())
                        ->isEqualTo(2.2)
                    ->integer($exclusion->getMaxDepth())
                        ->isEqualTo(42)
                    ->string($exclusion->getExcludeIf())
                        ->isEqualTo('foo')
            ->object($relation->getEmbedded())
                ->isInstanceOf('Hateoas\Configuration\Embedded')
                ->and($embedded = $relation->getEmbedded())
                    ->string($embedded->getContent())
                        ->isEqualTo('hello')
                    ->object($embedded->getExclusion())
                        ->isInstanceOf('Hateoas\Configuration\Exclusion')
                        ->and($exclusion = $embedded->getExclusion())
                            ->array($exclusion->getGroups())
                                ->isEqualTo(array('group3', 'group4'))
                            ->float($exclusion->getSinceVersion())
                                ->isEqualTo(1.1)
                            ->float($exclusion->getUntilVersion())
                                ->isEqualTo(2.3)
                            ->integer($exclusion->getMaxDepth())
                                ->isEqualTo(43)
                            ->string($exclusion->getExcludeIf())
                                ->isEqualTo('bar')
        ;

        /** @var $relations RelationProvider[] */
        $relationProviders = $classMetadata->getRelationProviders();

        $this->array($relationProviders);
        foreach ($relationProviders as $relationProvider) {
            $this
                ->object($relationProvider)
                    ->isInstanceOf('Hateoas\Configuration\RelationProvider')
            ;
        }

        $relationProvider = current($relationProviders);
        $this
            ->string($relationProvider->getName())
                ->isEqualTo('getRelations')
        ;
    }

    public function testEmptyClass()
    {
        $driver = $this->createDriver();
        $class = new \ReflectionClass('Hateoas\Tests\Fixtures\EmptyClass');
        $classMetadata = $driver->loadMetadataForClass($class);

        $this
            ->variable($classMetadata)
                ->isNull()
        ;
    }
}
