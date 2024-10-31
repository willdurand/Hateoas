<?php

declare(strict_types=1);

namespace Hateoas\Tests\Configuration\Metadata\Driver;

use Hateoas\Configuration\Provider\ChainProvider;
use Hateoas\Configuration\Provider\FunctionProvider;
use Hateoas\Configuration\Provider\RelationProviderInterface;
use Hateoas\Configuration\Provider\StaticMethodProvider;
use Hateoas\Configuration\Relation;
use Hateoas\Configuration\RelationProvider;
use Hateoas\Expression\LinkExpressionFunction;
use Hateoas\Tests\TestCase;
use JMS\Serializer\Expression\ExpressionEvaluator;
use JMS\Serializer\Type\Parser;
use JMS\Serializer\Type\ParserInterface;
use Metadata\Driver\DriverInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

abstract class AbstractDriverTestCase extends TestCase
{
    protected function getExpressionEvaluator()
    {
        $expressionLanguage = new ExpressionLanguage();
        $expressionLanguage->registerProvider(new LinkExpressionFunction());

        return new ExpressionEvaluator($expressionLanguage);
    }

    protected function createProvider(): RelationProviderInterface
    {
        return new ChainProvider([
            new FunctionProvider(),
            new StaticMethodProvider(),
        ]);
    }

    protected function createTypeParser(): ParserInterface
    {
        return new Parser();
    }

    protected function getUserClass(): string
    {
        return 'Hateoas\Tests\Fixtures\User';
    }

    /**
     * @return DriverInterface
     */
    abstract public function createDriver();

    public function testUser()
    {
        $driver = $this->createDriver();
        $class = new \ReflectionClass($this->getUserClass());
        $classMetadata = $driver->loadMetadataForClass($class);

        $exp = $this->getExpressionEvaluator();

        $this->assertInstanceOf('Hateoas\Configuration\Metadata\ClassMetadata', $classMetadata);

        /** @var Relation[] $relations */
        $relations = $classMetadata->getRelations();

        $this->assertIsArray($relations);
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
        $this->assertEquals(['id' => $exp->parse('object.getId()', ['object'])], $relation->getHref()->getParameters());
        $this->assertFalse($relation->getHref()->isAbsolute());
        $this->assertInstanceOf('Hateoas\Configuration\Embedded', $relation->getEmbedded());
        $this->assertEquals($exp->parse('object.getFoo()', ['object']), $relation->getEmbedded()->getContent());
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
        $this->assertEquals(['id' => $exp->parse('object.getId()', ['object'])], $relation->getHref()->getParameters());
        $this->assertTrue($relation->getHref()->isAbsolute());
        $this->assertNull($relation->getExclusion());

        $relation = $relations[$i++];
        $this->assertSame('boom', $relation->getName());
        $this->assertInstanceOf('Hateoas\Configuration\Route', $relation->getHref());
        $this->assertSame('user_get', $relation->getHref()->getName());
        $this->assertEquals(['id' => $exp->parse('object.getId()', ['object'])], $relation->getHref()->getParameters());
        $this->assertFalse($relation->getHref()->isAbsolute());
        $this->assertNull($relation->getExclusion());

        $relation = $relations[$i++];
        $this->assertSame('badaboom', $relation->getName());
        $this->assertNull($relation->getHref());
        $this->assertInstanceOf('Hateoas\Configuration\Embedded', $relation->getEmbedded());
        $this->assertEquals($exp->parse('object.getFoo()', ['object']), $relation->getEmbedded()->getContent());
        $this->assertNull($relation->getExclusion());

        $relation = $relations[$i++];
        $this->assertSame('hello', $relation->getName());
        $this->assertSame('/hello', $relation->getHref());
        $this->assertInstanceOf('Hateoas\Configuration\Exclusion', $relation->getExclusion());
        $this->assertSame(['group1', 'group2'], $relation->getExclusion()->getGroups());
        $this->assertSame('1', $relation->getExclusion()->getSinceVersion());
        $this->assertSame('2.2', $relation->getExclusion()->getUntilVersion());
        $this->assertSame(42, $relation->getExclusion()->getMaxDepth());
        $this->assertSame('foo', $relation->getExclusion()->getExcludeIf());
        $this->assertInstanceOf('Hateoas\Configuration\Embedded', $relation->getEmbedded());
        $this->assertSame('hello', $relation->getEmbedded()->getContent());
        $this->assertSame(['name' => 'string', 'params' => []], $relation->getEmbedded()->getType());
        $this->assertInstanceOf('Hateoas\Configuration\Exclusion', $relation->getEmbedded()->getExclusion());
        $this->assertSame(['group3', 'group4'], $relation->getEmbedded()->getExclusion()->getGroups());
        $this->assertSame('1.1', $relation->getEmbedded()->getExclusion()->getSinceVersion());
        $this->assertSame('2.3', $relation->getEmbedded()->getExclusion()->getUntilVersion());
        $this->assertSame(43, $relation->getEmbedded()->getExclusion()->getMaxDepth());
        $this->assertSame('bar', $relation->getEmbedded()->getExclusion()->getExcludeIf());

        $relation = $relations[$i++];
        $this->assertSame('attribute_with_expression', $relation->getName());
        $this->assertEquals(['baz' => $exp->parse('object.getId()', ['object'])], $relation->getAttributes());

        /** @var RelationProvider[] $relations */
        $relations = $classMetadata->getRelations();
        $this->assertIsArray($relations);
        $this->assertCount(8, $relations);

//        $relation = current($relations);
//        $this->assertSame('getRelations', $relation->getName());
    }

    public function testEmptyClass()
    {
        $driver = $this->createDriver();
        $class = new \ReflectionClass('Hateoas\Tests\Fixtures\EmptyClass');
        $classMetadata = $driver->loadMetadataForClass($class);

        $this->assertNull($classMetadata);
    }
}
