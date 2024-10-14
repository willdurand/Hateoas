<?php

declare(strict_types=1);

namespace Hateoas\Tests\Configuration\Metadata\Driver;

use Hateoas\Configuration\Metadata\Driver\AttributeDriver;
use Hateoas\Tests\Fixtures\UserPhpAttributes;

class AttributeDriverTest extends AbstractDriverTest
{
    public function setUp(): void
    {
        if (PHP_VERSION_ID < 80100) {
            $this->markTestSkipped('AttributeDriver is available as of PHP 8.1.0');
        }
    }

    public function createDriver()
    {
        return new AttributeDriver(
            $this->getExpressionEvaluator(),
            $this->createProvider(),
            $this->createTypeParser()
        );
    }

    protected function getUserClass(): string
    {
        return UserPhpAttributes::class;
    }
}
