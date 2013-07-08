<?php

namespace tests\Hateoas\Configuration\Metadata\Driver;

use Metadata\Driver\FileLocator;
use Hateoas\Configuration\Metadata\Driver\YamlDriver as TestedYamlDriver;

class YamlDriver extends AbstractDriverTest
{
    public function createDriver()
    {
        return new TestedYamlDriver(new FileLocator(array(
            'tests\fixtures' => $this->rootPath() . '/fixtures/config',
        )));
    }
}
