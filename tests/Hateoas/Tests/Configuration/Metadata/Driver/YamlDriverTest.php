<?php

declare(strict_types=1);

namespace Hateoas\Tests\Configuration\Metadata\Driver;

use Hateoas\Configuration\Metadata\Driver\YamlDriver;
use Metadata\Driver\FileLocator;

class YamlDriverTest extends AbstractDriverTestCase
{
    public function createDriver()
    {
        return new YamlDriver(new FileLocator([
            'Hateoas\Tests\Fixtures' => $this->rootPath() . '/Fixtures/config',
        ]), $this->getExpressionEvaluator(), $this->createProvider(), $this->createTypeParser());
    }
}
