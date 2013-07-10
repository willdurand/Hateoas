<?php

namespace tests\Hateoas\Configuration;

use tests\TestCase;
use Hateoas\Configuration\Relation as Relation_;
use Hateoas\Configuration\RelationsRepository as TestedRelationsRepository;

class RelationsRepository extends TestCase
{
    public function testEmptyGetRelations()
    {
        $relationsRepository = new TestedRelationsRepository();
        $object = new \StdClass();

        $this
            ->array($relationsRepository->getRelations($object))
                ->isEmpty()
        ;
    }

    public function testAddRelation()
    {
        $relationsRepository = new TestedRelationsRepository();
        $object = new \StdClass();

        $relation1 = new Relation_('', '');
        $relation2 = new Relation_('', '');

        $relationsRepository->addRelation($object, $relation1);
        $relationsRepository->addRelation($object, $relation2);

        $this
            ->array($relationsRepository->getRelations($object))
                ->contains($relation1)
                ->contains($relation2)
        ;
    }

    public function testAddClassRelation()
    {
        $relationsRepository = new TestedRelationsRepository();
        $object1 = new \StdClass();
        $object2 = new \StdClass();

        $relation1 = new Relation_('', '');
        $relation2 = new Relation_('', '');

        $relationsRepository->addClassRelation('StdClass', $relation1);
        $relationsRepository->addClassRelation('StdClass', $relation2);

        $this
            ->array($relationsRepository->getRelations($object1))
                ->contains($relation1)
                ->contains($relation2)
            ->array($relationsRepository->getRelations($object2))
                ->contains($relation1)
                ->contains($relation2)
        ;
    }

    public function testMetadataFactoryRelations()
    {
        $relations = array(
            new Relation_('', ''),
            new Relation_('', ''),
        );

        $metadataFactory = new \mock\Metadata\MetadataFactoryInterface();
        $metadataFactory->getMockController()->getMetadataForClass = function () use ($relations) {
            $classMetadata = new \mock\Hateoas\Configuration\Metadata\ClassMetadataInterface();
            $classMetadata->getMockController()->getRelations = function () use ($relations) {
                return $relations;
            };

            return $classMetadata;
        };
        $relationsRepository = new TestedRelationsRepository($metadataFactory);

        $this
            ->array($relationsRepository->getRelations(new \StdClass()))
                ->isEqualTo($relations)
            ->mock($metadataFactory)
                ->call('getMetadataForClass')
                    ->withArguments('stdClass')
                    ->once()
        ;
    }
}
