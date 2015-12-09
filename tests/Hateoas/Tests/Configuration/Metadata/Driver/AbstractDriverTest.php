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

        $this->assertInstanceOf('Hateoas\Configuration\Metadata\ClassMetadata', $classMetadata);

        /** @var $relations Relation[] */
        $relations = $classMetadata->getRelations();

        $this->assertInternalType('array', $relations);
        foreach ($relations as $relation) {
            $this->assertInstanceOf('Hateoas\Configuration\Relation', $relation);
        }

        $i = 0;

        $relation = $relations[$i++];
        $this->assertSame('self', $relation->getName());
        $this->assertSame('http://hateoas.web/user/42', $relation->getHref());
        $this->assertSame(['type' => 'application/json'], $relation->getAttributes());
        $this->assertNull($relation->getEmbedded());
        $this->assertNull($relation->getExclusion());

        $relation = $relations[$i++];
        $this->assertSame('foo', $relation->getName());
        $this->assertInstanceOf('Hateoas\Configuration\Route', $relation->getHref());
        $this->assertSame('user_get', $relation->getHref()->getName());
        $this->assertSame(['id' => 'expr(object.getId())'], $relation->getHref()->getParameters());
        $this->assertFalse($relation->getHref()->isAbsolute());
        $this->assertInstanceOf('Hateoas\Configuration\Embedded', $relation->getEmbedded());
        $this->assertSame('expr(object.getFoo())', $relation->getEmbedded()->getContent());
        $this->assertNull($relation->getEmbedded()->getXmlElementName());
        $this->assertNull($relation->getEmbedded()->getExclusion());

        $relation = $relations[$i++];
        $this->assertSame('bar', $relation->getName());
        $this->assertSame('foo', $relation->getHref());
        $this->assertInstanceOf('Hateoas\Configuration\Embedded', $relation->getEmbedded());
        $this->assertSame('data', $relation->getEmbedded()->getContent());
        $this->assertSame('barTag', $relation->getEmbedded()->getXmlElementName());
        $this->assertNull($relation->getEmbedded()->getExclusion());

        $relation = $relations[$i++];
        $this->assertSame('baz', $relation->getName());
        $this->assertInstanceOf('Hateoas\Configuration\Route', $relation->getHref());
        $this->assertSame('user_get', $relation->getHref()->getName());
        $this->assertSame(['id' => 'expr(object.getId())'], $relation->getHref()->getParameters());
        $this->assertTrue($relation->getHref()->isAbsolute());
        $this->assertNull($relation->getExclusion());

        $relation = $relations[$i++];
        $this->assertSame('boom', $relation->getName());
        $this->assertInstanceOf('Hateoas\Configuration\Route', $relation->getHref());
        $this->assertSame('user_get', $relation->getHref()->getName());
        $this->assertSame(['id' => 'expr(object.getId())'], $relation->getHref()->getParameters());
        $this->assertFalse($relation->getHref()->isAbsolute());
        $this->assertNull($relation->getExclusion());

        $relation = $relations[$i++];
        $this->assertSame('badaboom', $relation->getName());
        $this->assertNull($relation->getHref());
        $this->assertInstanceOf('Hateoas\Configuration\Embedded', $relation->getEmbedded());
        $this->assertSame('expr(object.getFoo())', $relation->getEmbedded()->getContent());
        $this->assertNull($relation->getExclusion());

        $relation = $relations[$i++];
        $this->assertSame('hello', $relation->getName());
        $this->assertSame('/hello', $relation->getHref());
        $this->assertInstanceOf('Hateoas\Configuration\Exclusion', $relation->getExclusion());
        $this->assertSame(['group1', 'group2'], $relation->getExclusion()->getGroups());
        $this->assertSame(1.0, $relation->getExclusion()->getSinceVersion());
        $this->assertSame(2.2, $relation->getExclusion()->getUntilVersion());
        $this->assertSame(42, $relation->getExclusion()->getMaxDepth());
        $this->assertSame('foo', $relation->getExclusion()->getExcludeIf());
        $this->assertInstanceOf('Hateoas\Configuration\Embedded', $relation->getEmbedded());
        $this->assertSame('hello', $relation->getEmbedded()->getContent());
        $this->assertInstanceOf('Hateoas\Configuration\Exclusion', $relation->getEmbedded()->getExclusion());
        $this->assertSame(['group3', 'group4'], $relation->getEmbedded()->getExclusion()->getGroups());
        $this->assertSame(1.1, $relation->getEmbedded()->getExclusion()->getSinceVersion());
        $this->assertSame(2.3, $relation->getEmbedded()->getExclusion()->getUntilVersion());
        $this->assertSame(43, $relation->getEmbedded()->getExclusion()->getMaxDepth());
        $this->assertSame('bar', $relation->getEmbedded()->getExclusion()->getExcludeIf());

        /** @var $relations RelationProvider[] */
        $relationProviders = $classMetadata->getRelationProviders();

        $this->assertInternalType('array', $relationProviders);
        foreach ($relationProviders as $relationProvider) {
            $this->assertInstanceOf('Hateoas\Configuration\RelationProvider', $relationProvider);
        }

        $relationProvider = current($relationProviders);
        $this->assertSame('getRelations', $relationProvider->getName());
    }

    public function testEmptyClass()
    {
        $driver = $this->createDriver();
        $class = new \ReflectionClass('Hateoas\Tests\Fixtures\EmptyClass');
        $classMetadata = $driver->loadMetadataForClass($class);

        $this->assertNull($classMetadata);
    }
}
