<?php

declare(strict_types=1);

namespace Hateoas\Tests\Configuration\Metadata\Driver;

use Hateoas\Configuration\Metadata\Driver\XmlDriver;
use Metadata\Driver\FileLocator;

class XmlDriverTest extends AbstractDriverTestCase
{
    public function createDriver()
    {
        return new XmlDriver(new FileLocator([
            'Hateoas\Tests\Fixtures' => $this->rootPath() . '/Fixtures/config',
        ]), $this->getExpressionEvaluator(), $this->createProvider(), $this->createTypeParser());
    }
}
