<?php

namespace tests\Hateoas\Factory;

use Hateoas\Configuration\Relation;
use Hateoas\Factory\LinksFactory as TestedLinksFactory;
use Hateoas\Model\Link;
use JMS\Serializer\SerializationContext;
use tests\TestCase;

class LinksFactory extends TestCase
{
    public function test()
    {
        $relations = array(
            new Relation('self', '/users/1'),
            new Relation('manager', '/users/2'),
        );
        $link = new Link('', '');

        $relationsRepository = $this->mockRelationsRepository();
        $relationsRepository->getMockController()->getRelations = function () use ($relations) {
            return $relations;
        };
        $linkFactory = $this->mockLinkFactory();
        $linkFactory->getMockController()->createLink = function () use ($link) {
            return $link;
        };
        $exclusionManager = $this->mockExclusionManager();
        $exclusionManager->getMockController()->shouldSkipLink = function ($object, $relation) use ($relations) {
            return $relation === $relations[0];
        };

        $linksFactory = new TestedLinksFactory($relationsRepository, $linkFactory, $exclusionManager);

        $links = $linksFactory->createLinks($object = new \StdClass(), $context = SerializationContext::create());

        $this
            ->array($links)
                ->hasSize(1)
                ->contains($link)
            ->mock($relationsRepository)
                ->call('getRelations')
                    ->withArguments($object)
                    ->once()
            ->mock($exclusionManager)
                ->call('shouldSkipLink')
                    ->withArguments($object, $relations[0], $context)
                    ->once()
                ->call('shouldSkipLink')
                    ->withArguments($object, $relations[1], $context)
                    ->once()
            ->mock($linkFactory)
                ->call('createLink')
                    ->withArguments($object, $relations[1])
                    ->once()
        ;
    }

    private function mockRelationsRepository()
    {
        $this->mockGenerator->orphanize('__construct');

        return new \mock\Hateoas\Configuration\RelationsRepository();
    }

    private function mockLinkFactory()
    {
        $this->mockGenerator->orphanize('__construct');

        return new \mock\Hateoas\Factory\LinkFactory();
    }

    private function mockExclusionManager()
    {
        $this->mockGenerator->orphanize('__construct');

        return new \mock\Hateoas\Serializer\ExclusionManager();
    }
}
