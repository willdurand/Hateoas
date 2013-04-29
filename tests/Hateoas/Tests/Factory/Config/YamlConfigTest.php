<?php

namespace Hateoas\Tests\Factory\Config;

use Hateoas\Factory\Config\YamlConfig;
use Hateoas\Tests\TestCase;

class YamlConfigTest extends TestCase
{
    public function testParseFile()
    {
        $config = new YamlConfig(__DIR__ . '/../../Fixtures/hateoas.yml');

        $this->assertTrue(is_array($config->getResourceDefinitions()));
        $this->assertTrue(is_array($config->getCollectionDefinitions()));

        $this->assertCount(1, $config->getResourceDefinitions());
        $this->assertCount(1, $config->getCollectionDefinitions());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testParseInvalidFile()
    {
        $config = new YamlConfig(__DIR__ . '/../../Fixtures/hateoas-404.yml');
    }
}
