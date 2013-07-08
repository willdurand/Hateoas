<?php

namespace tests\Hateoas\Configuration;

use tests\TestCase;
use Hateoas\Configuration\Relation as Relation_;
use Hateoas\Configuration\RelationsManager as TestedRelationsManager;

class RelationsManager extends TestCase
{
    public function testEmptyGetRelations()
    {
        $relationsManager = new TestedRelationsManager();
        $object = new \StdClass();

        $this
            ->array($relationsManager->getRelations($object))
                ->isEmpty()
        ;
    }

    public function testAddRelation()
    {
        $relationsManager = new TestedRelationsManager();
        $object = new \StdClass();

        $relation1 = new Relation_('', '');
        $relation2 = new Relation_('', '');

        $relationsManager->addRelation($object, $relation1);
        $relationsManager->addRelation($object, $relation2);

        $this
            ->array($relationsManager->getRelations($object))
                ->contains($relation1)
                ->contains($relation2)
        ;
    }

    public function testAddClassRelation()
    {
        $relationsManager = new TestedRelationsManager();
        $object1 = new \StdClass();
        $object2 = new \StdClass();

        $relation1 = new Relation_('', '');
        $relation2 = new Relation_('', '');

        $relationsManager->addClassRelation('StdClass', $relation1);
        $relationsManager->addClassRelation('StdClass', $relation2);

        $this
            ->array($relationsManager->getRelations($object1))
                ->contains($relation1)
                ->contains($relation2)
            ->array($relationsManager->getRelations($object2))
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
        $relationsManager = new TestedRelationsManager($metadataFactory);

        $this
            ->array($relationsManager->getRelations(new \StdClass()))
                ->isEqualTo($relations)
            ->mock($metadataFactory)
                ->call('getMetadataForClass')
                    ->withArguments('stdClass')
                    ->once()
        ;
    }
}
