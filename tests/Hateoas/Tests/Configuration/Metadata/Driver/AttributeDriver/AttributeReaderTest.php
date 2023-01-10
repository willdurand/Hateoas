<?php

declare(strict_types=1);

namespace Hateoas\Tests\Configuration\Metadata\Driver\AttributeDriver;

use Doctrine\Common\Annotations\Reader;
use Hateoas\Configuration\Annotation;
use Hateoas\Configuration\Metadata\Driver\AttributeDriver\AttributeReader;
use Hateoas\Tests\Fixtures\EmptyClass;
use Hateoas\Tests\Fixtures\NoAnnotations;
use Hateoas\Tests\Fixtures\User;
use PHPUnit\Framework\TestCase;

class AttributeReaderTest extends TestCase
{
    public function setUp(): void
    {
        if (PHP_VERSION_ID < 80100) {
            $this->markTestSkipped('Attributes are available only on php 8.1 or higher');
        }
    }

    public function testGetClassAnnotations()
    {
        $delegate = $this->createMock(Reader::class);
        $reader = new AttributeReader($delegate);
        $refl = new \ReflectionClass(User::class);

        $delegate
            ->expects($this->once())
            ->method('getClassAnnotations')
            ->with($refl)
            ->willReturn([])
        ;

        $result = $reader->getClassAnnotations($refl);

        $this->assertCount(9, $result);
    }

    public function testGetClassAnnotationsDelegated()
    {
        $delegate = $this->createMock(Reader::class);
        $reader = new AttributeReader($delegate);
        $refl = new \ReflectionClass(EmptyClass::class);
        $annotation = new Annotation\RelationProvider();

        $delegate
            ->expects($this->once())
            ->method('getClassAnnotations')
            ->with($refl)
            ->willReturn([$annotation])
        ;

        $result = $reader->getClassAnnotations($refl);

        $this->assertSame([$annotation], $result);
    }

    public function testGetClassAnnotation()
    {
        $delegate = $this->createMock(Reader::class);
        $reader = new AttributeReader($delegate);
        $refl = new \ReflectionClass(User::class);

        $delegate
            ->expects($this->once())
            ->method('getClassAnnotation')
            ->with($refl, Annotation\RelationProvider::class)
            ->willReturn(null)
        ;

        $result = $reader->getClassAnnotation($refl, Annotation\RelationProvider::class);

        $this->assertInstanceOf(Annotation\RelationProvider::class, $result);
    }

    public function testGetClassAnnotationDelegated()
    {
        $delegate = $this->createMock(Reader::class);
        $reader = new AttributeReader($delegate);
        $refl = new \ReflectionClass(EmptyClass::class);
        $annotation = new Annotation\RelationProvider();

        $delegate
            ->expects($this->once())
            ->method('getClassAnnotation')
            ->with($refl, Annotation\RelationProvider::class)
            ->willReturn($annotation)
        ;

        $result = $reader->getClassAnnotation($refl, Annotation\RelationProvider::class);

        $this->assertSame($annotation, $result);
    }

    public function testGetMethodAnnotations()
    {
        $delegate = $this->createMock(Reader::class);
        $reader = new AttributeReader($delegate);
        $refl = new \ReflectionMethod(NoAnnotations::class, 'id');

        $delegate
            ->expects($this->once())
            ->method('getMethodAnnotations')
            ->with($refl)
            ->willReturn([])
        ;

        $result = $reader->getMethodAnnotations($refl);

        $this->assertEmpty($result);
    }

    public function testGetMethodAnnotationsDelegated()
    {
        $delegate = $this->createMock(Reader::class);
        $reader = new AttributeReader($delegate);
        $refl = new \ReflectionMethod(NoAnnotations::class, 'id');
        $annotation = new Annotation\RelationProvider();

        $delegate
            ->expects($this->once())
            ->method('getMethodAnnotations')
            ->with($refl)
            ->willReturn([$annotation])
        ;

        $result = $reader->getMethodAnnotations($refl);

        $this->assertSame([$annotation], $result);
    }

    public function testGetMethodAnnotation()
    {
        $delegate = $this->createMock(Reader::class);
        $reader = new AttributeReader($delegate);
        $refl = new \ReflectionMethod(NoAnnotations::class, 'id');

        $delegate
            ->expects($this->once())
            ->method('getMethodAnnotation')
            ->with($refl, Annotation\RelationProvider::class)
            ->willReturn(null)
        ;

        $result = $reader->getMethodAnnotation($refl, Annotation\RelationProvider::class);

        $this->assertEmpty($result);
    }

    public function testGetMethodAnnotationDelegated()
    {
        $delegate = $this->createMock(Reader::class);
        $reader = new AttributeReader($delegate);
        $refl = new \ReflectionMethod(NoAnnotations::class, 'id');
        $annotation = new Annotation\RelationProvider();

        $delegate
            ->expects($this->once())
            ->method('getMethodAnnotation')
            ->with($refl, Annotation\RelationProvider::class)
            ->willReturn($annotation)
        ;

        $result = $reader->getMethodAnnotation($refl, Annotation\RelationProvider::class);

        $this->assertSame($annotation, $result);
    }

    public function testGetPropertyAnnotations()
    {
        $delegate = $this->createMock(Reader::class);
        $reader = new AttributeReader($delegate);
        $refl = new \ReflectionProperty(NoAnnotations::class, 'id');

        $delegate
            ->expects($this->once())
            ->method('getPropertyAnnotations')
            ->with($refl)
            ->willReturn([])
        ;

        $result = $reader->getPropertyAnnotations($refl);

        $this->assertEmpty($result);
    }

    public function testGetPropertyAnnotationsDelegated()
    {
        $delegate = $this->createMock(Reader::class);
        $reader = new AttributeReader($delegate);
        $refl = new \ReflectionProperty(NoAnnotations::class, 'id');
        $annotation = new Annotation\RelationProvider();

        $delegate
            ->expects($this->once())
            ->method('getPropertyAnnotations')
            ->with($refl)
            ->willReturn([$annotation])
        ;

        $result = $reader->getPropertyAnnotations($refl);

        $this->assertSame([$annotation], $result);
    }

    public function testGetPropertyAnnotation()
    {
        $delegate = $this->createMock(Reader::class);
        $reader = new AttributeReader($delegate);
        $refl = new \ReflectionProperty(NoAnnotations::class, 'id');

        $delegate
            ->expects($this->once())
            ->method('getPropertyAnnotation')
            ->with($refl, Annotation\RelationProvider::class)
            ->willReturn(null)
        ;

        $result = $reader->getPropertyAnnotation($refl, Annotation\RelationProvider::class);

        $this->assertEmpty($result);
    }

    public function testGetPropertyAnnotationDelegated()
    {
        $delegate = $this->createMock(Reader::class);
        $reader = new AttributeReader($delegate);
        $refl = new \ReflectionProperty(NoAnnotations::class, 'id');
        $annotation = new Annotation\RelationProvider();

        $delegate
            ->expects($this->once())
            ->method('getPropertyAnnotation')
            ->with($refl, Annotation\RelationProvider::class)
            ->willReturn($annotation)
        ;

        $result = $reader->getPropertyAnnotation($refl, Annotation\RelationProvider::class);

        $this->assertSame($annotation, $result);
    }
}
