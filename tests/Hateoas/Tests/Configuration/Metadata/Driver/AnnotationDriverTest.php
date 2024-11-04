<?php

declare(strict_types=1);

namespace Hateoas\Tests\Configuration\Metadata\Driver;

use Doctrine\Common\Annotations\AnnotationReader;
use Hateoas\Configuration\Metadata\Driver\AnnotationDriver;

class AnnotationDriverTest extends AbstractDriverTestCase
{
    public function setUp(): void
    {
        if (!class_exists(AnnotationReader::class)) {
            $this->markTestSkipped('AnnotationReader is not available');
        }
    }

    public function createDriver()
    {
        return new AnnotationDriver(
            new AnnotationReader(),
            $this->getExpressionEvaluator(),
            $this->createProvider(),
            $this->createTypeParser()
        );
    }
}
