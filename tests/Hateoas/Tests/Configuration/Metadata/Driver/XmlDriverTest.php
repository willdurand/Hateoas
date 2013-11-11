<?php

namespace Hateoas\Tests\Configuration\Metadata\Driver;

use Metadata\Driver\FileLocator;
use Hateoas\Configuration\Metadata\Driver\XmlDriver;

class XmlDriverTest extends AbstractDriverTest
{
    public function createDriver()
    {
        return new XmlDriver(new FileLocator(array(
            'Hateoas\Tests\Fixtures' => $this->rootPath() . '/Fixtures/config',
        )));
    }
}
