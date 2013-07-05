<?php

namespace Hateoas\Tests\Factory\Config;

use Hateoas\Factory\Config\YamlConfig;
use Hateoas\Tests\TestCase;
use org\bovigo\vfs\vfsStream;

class YamlConfigTest extends TestCase
{
    private $cachePath;

    public function setUp()
    {
        $this->cachePath = vfsStream::setup('cache');
    }

    public function testParseFile()
    {
        $config = new YamlConfig(__DIR__ . '/../../Fixtures/hateoas.yml');

        $this->assertTrue(is_array($config->getResourceDefinitions()));
        $this->assertTrue(is_array($config->getCollectionDefinitions()));

        $this->assertCount(1, $config->getResourceDefinitions());
        $this->assertCount(1, $config->getCollectionDefinitions());
    }

    public function testParseFileAndCachedIt()
    {
        $config = new YamlConfig(__DIR__ . '/../../Fixtures/hateoas.yml');
        $this->assertFalse($this->cachePath->hasChildren());

        $cachedConfig = new YamlConfig(__DIR__ . '/../../Fixtures/hateoas.yml', vfsStream::url('cache'));
        $this->assertTrue($this->cachePath->hasChildren());

        $this->assertSame($config->getResourceDefinitions(), $cachedConfig->getResourceDefinitions());
        $this->assertSame($config->getCollectionDefinitions(), $cachedConfig->getCollectionDefinitions());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testParseInvalidFile()
    {
        $config = new YamlConfig(__DIR__ . '/../../Fixtures/hateoas-404.yml');
    }
}
