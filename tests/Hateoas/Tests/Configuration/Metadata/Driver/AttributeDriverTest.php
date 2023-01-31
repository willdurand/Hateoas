<?php

declare(strict_types=1);

namespace Hateoas\Tests\Configuration\Metadata\Driver;

use Doctrine\Common\Annotations\Reader;
use Hateoas\Configuration\Metadata\Driver\AnnotationDriver;
use Hateoas\Configuration\Metadata\Driver\AttributeDriver\AttributeReader;

class AttributeDriverTest extends AbstractDriverTest
{
    protected function getUserClass(): string
    {
        return 'Hateoas\Tests\Fixtures\UserPhpAttributes';
    }

    public function testUser()
    {
        if (PHP_VERSION_ID < 80100) {
            $this->markTestSkipped('Attributes are available only on php 8.1 or higher');
        }

        parent::testUser();
    }

    public function createDriver()
    {
        $driver = $this->createMock(Reader::class);
        $driver
            ->expects($this->once())
            ->method('getClassAnnotations')
            ->willReturn([]);

        return new AnnotationDriver(
            new AttributeReader($driver),
            $this->getExpressionEvaluator(),
            $this->createProvider(),
            $this->createTypeParser()
        );
    }
}
