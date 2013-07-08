<?php

namespace tests\Hateoas\Factory;

use Hateoas\Configuration\Relation;
use tests\TestCase;
use Hateoas\Factory\EmbeddedMapFactory as TestedEmbeddedMapFactory;

class EmbeddedMapFactory extends TestCase
{
    public function test()
    {
        $relations = array(
            new Relation('self', '/users/1'),
            new Relation('friend', '/users/42', '@this.friend'),
            new Relation('manager', '/users/42', '@this.manager'),
        );

        $this->mockGenerator->orphanize('__construct');
        $relationsManager = new \mock\Hateoas\Configuration\RelationsManagerInterface();
        $relationsManager->getMockController()->getRelations = function () use ($relations) {
            return $relations;
        };

        $handlerManager = new \mock\Hateoas\Handler\HandlerManager();
        $handlerManager->getMockController()->transform = function () {
            return 42;
        };

        $embeddedMapFactory = new TestedEmbeddedMapFactory($relationsManager, $handlerManager);

        $object = new \StdClass();

        $embeddedMap = $embeddedMapFactory->create($object);
        $expectedEmbeddedMap = array(
            'friend' => 42,
            'manager' => 42,
        );

        $this
            ->array($embeddedMap)
                ->isEqualTo($expectedEmbeddedMap)
            ->mock($relationsManager)
                ->call('getRelations')
                    ->withArguments($object)
                    ->once()
            ->mock($handlerManager)
                ->call('transform')
                    ->withArguments('@this.friend', $object)
                    ->once()
                ->call('transform')
                    ->withArguments('@this.manager', $object)
                    ->once()
        ;
    }
}
