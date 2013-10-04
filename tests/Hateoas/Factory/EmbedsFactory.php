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
            new Relation('friend', '/users/42', 'expr(object.getFriend())'),
            new Relation('expr(object.getManagerRel())', '/users/42', 'expr(object.getManager())'),
        );

        $this->mockGenerator->orphanize('__construct');
        $relationsRepository = new \mock\Hateoas\Configuration\RelationsRepository();
        $relationsRepository->getMockController()->getRelations = function () use ($relations) {
            return $relations;
        };

        $this->mockGenerator->orphanize('__construct');
        $expressionEvaluator = new \mock\Hateoas\Expression\ExpressionEvaluator();
        $expressionEvaluator->getMockController()->evaluate = function ($expression) {
            if (strpos($expression, 'expr(') === 0) {
                return 42;
            }

            return $expression;
        };

        $embedsFactory = new TestedEmbedsFactory($relationsRepository, $expressionEvaluator);

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
            ->mock($expressionEvaluator)
                ->call('evaluate')
                    ->withArguments('expr(object.getFriend())', $object)
                    ->once()
                ->call('evaluate')
                    ->withArguments('expr(object.getManager())', $object)
                    ->once()
                ->call('evaluate')
                    ->withArguments('expr(object.getManagerRel())', $object)
                    ->once()
        ;
    }
}
