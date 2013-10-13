<?php

namespace Hateoas\Tests\Configuration\Metadata\Driver;

use Metadata\Driver\FileLocator;
use Hateoas\Configuration\Metadata\Driver\YamlDriver;

class YamlDriverTest extends AbstractDriverTest
{
    public function createDriver()
    {
        return new YamlDriver(new FileLocator(array(
            'Hateoas\Tests\Fixtures' => $this->rootPath() . '/Fixtures/config',
        )));
    }
}
