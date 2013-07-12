<?php

namespace tests\Hateoas\Factory;

use Hateoas\Configuration\Relation;
use Hateoas\Factory\EmbedsFactory as TestedEmbedsFactory;
use tests\TestCase;

class EmbedsFactory extends TestCase
{
    public function test()
    {
        $relations = array(
            new Relation('self', '/users/1'),
            new Relation('friend', '/users/42', '@this.friend'),
            new Relation('@this.managerRel', '/users/42', '@this.manager'),
        );

        $this->mockGenerator->orphanize('__construct');
        $relationsRepository = new \mock\Hateoas\Configuration\RelationsRepository();
        $relationsRepository->getMockController()->getRelations = function () use ($relations) {
            return $relations;
        };

        $handlerManager = new \mock\Hateoas\Handler\HandlerManager();
        $handlerManager->getMockController()->transform = function ($value) {
            if ($value[0] == '@') {
                return 42;
            }

            return $value;
        };

        $embedsFactory = new TestedEmbedsFactory($relationsRepository, $handlerManager);

        $object = new \StdClass();

        $embeds = $embedsFactory->create($object);

        $this
            ->array($embeds)
                ->hasSize(2)
            ->object($embeds[0])
                ->isInstanceOf('Hateoas\Model\Embed')
                ->variable($embeds[0]->getRel())
                    ->isEqualTo('friend')
                ->variable($embeds[0]->getData())
                    ->isEqualTo(42)
            ->object($embeds[1])
                ->isInstanceOf('Hateoas\Model\Embed')
                ->variable($embeds[1]->getRel())
                    ->isEqualTo(42)
                ->variable($embeds[1]->getData())
                    ->isEqualTo(42)
        ;

        $this
            ->mock($relationsRepository)
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
                ->call('transform')
                    ->withArguments('@this.managerRel', $object)
                    ->once()
        ;
    }
}
