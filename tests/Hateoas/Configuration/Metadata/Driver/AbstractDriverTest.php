<?php

namespace tests\Hateoas\Configuration\Metadata\Driver;

use Hateoas\Configuration\Relation;
use Metadata\Driver\DriverInterface;
use tests\TestCase;

abstract class AbstractDriverTest extends TestCase
{
    /**
     * @return DriverInterface
     */
    abstract public function createDriver();

    public function testUser()
    {
        $driver = $this->createDriver();
        $class = new \ReflectionClass('tests\fixtures\User');
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
            ->variable($relation->getEmbed())
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
                            'id' => '@this.id',
                        ))
                ->string($relation->getEmbed())
                    ->isEqualTo('@this.foo')
        ;
    }

    public function testEmptyClass()
    {
        $driver = $this->createDriver();
        $class = new \ReflectionClass('tests\fixtures\EmptyClass');
        $classMetadata = $driver->loadMetadataForClass($class);

        $this
            ->variable($classMetadata)
                ->isNull()
        ;
    }
}
