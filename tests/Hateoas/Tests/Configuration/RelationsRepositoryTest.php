<?php

namespace Hateoas\Tests\Configuration;

use Hateoas\Tests\TestCase;
use Hateoas\Configuration\Relation;
use Hateoas\Configuration\RelationsRepository;
use Prophecy\Argument;

class RelationsRepositoryTest extends TestCase
{
    public function testEmptyGetRelations()
    {
        $relationsRepository = new RelationsRepository(
            $this->prophesizeMetadataFactory()->reveal(),
            $this->prophesizeRelationProvider()->reveal()
        );
        $object = new \StdClass();

        $this->assertEmpty($relationsRepository->getRelations($object));
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

        $this->assertSame($relations, $relationsRepository->getRelations(new \StdClass()));
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

        $this->assertSame($relations, $relationsRepository->getRelations($object));
    }

    private function prophesizeRelationProvider($relations = array(), $object = null)
    {
        $relationProviderProphecy = $this->prophesize('Hateoas\Configuration\Provider\RelationProvider');
        $relationProviderProphecy
            ->getRelations($object ?: Argument::any())
            ->willReturn($relations)
        ;

        return $relationProviderProphecy;
    }

    private function prophesizeMetadataFactory()
    {
        return $this->prophesize('Metadata\MetadataFactoryInterface');
    }
}
