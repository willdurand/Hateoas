<?php

declare(strict_types=1);

namespace Hateoas\Tests\Configuration\Metadata\Driver;

use Hateoas\Configuration\Metadata\Driver\AttributeDriver;
use Hateoas\Tests\Fixtures\Attribute\User;

class AttributeDriverTest extends AbstractDriverTestCase
{
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
        return User::class;
    }
}
