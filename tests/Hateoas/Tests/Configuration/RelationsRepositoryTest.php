<?php

namespace Hateoas\Tests\Configuration;

use Hateoas\Tests\TestCase;
use Hateoas\Configuration\Relation;
use Hateoas\Configuration\RelationsRepository;

class RelationsRepositoryTest extends TestCase
{
    public function testEmptyGetRelations()
    {
        $relationsRepository = new RelationsRepository(
            $this->prophesizeMetadataFactory()->reveal(),
            $this->prophesizeRelationProvider()->reveal()
        );
        $object = new \StdClass();

        $this
            ->array($relationsRepository->getRelations($object))
                ->isEmpty()
        ;
    }

    public function testMetadataFactoryRelations()
    {
        $relations = array(
            new Relation('', ''),
            new Relation('', ''),
        );

        $classMetadataProphecy = $this->prophesize('Hateoas\Configuration\Metadata\ClassMetadataInterface');
        $classMetadataProphecy
            ->getRelations()
            ->willReturn($relations)
            ->shouldBeCalledTimes(1)
        ;
        $metadataFactoryProphecy = $this->prophesizeMetadataFactory();
        $metadataFactoryProphecy
            ->getMetadataForClass('stdClass')
            ->willReturn($classMetadataProphecy->reveal())
        ;
        $relationsRepository = new RelationsRepository(
            $metadataFactoryProphecy->reveal(),
            $this->prophesizeRelationProvider()->reveal()
        );

        $this
            ->array($relationsRepository->getRelations(new \StdClass()))
                ->isEqualTo($relations)
        ;
    }

    public function testRelationProviderRelations()
    {
        $relations = array(
            new Relation('', ''),
            new Relation('', ''),
        );

        $object = new \StdClass();

        $relationsRepository = new RelationsRepository(
            $this->prophesizeMetadataFactory()->reveal(),
            $this->prophesizeRelationProvider($relations, $object)->reveal()
        );

        $this
            ->array($relationsRepository->getRelations($object))
                ->isEqualTo($relations)
        ;
    }

    private function prophesizeRelationProvider($relations = array(), $object = null)
    {
        $relationProviderProphecy = $this->prophesize('Hateoas\Configuration\Provider\RelationProvider');
        $relationProviderProphecy
            ->getRelations($object ?: $this->arg->any())
            ->willReturn($relations)
        ;

        return $relationProviderProphecy;
    }

    private function prophesizeMetadataFactory()
    {
        return $this->prophesize('Metadata\MetadataFactoryInterface');
    }
}
