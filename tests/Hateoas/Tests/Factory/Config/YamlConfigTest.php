<?php

namespace Hateoas\Tests\Factory\Config;

use Hateoas\Factory\Config\YamlConfig;
use Hateoas\Tests\TestCase;

class YamlConfigTest extends TestCase
{
    private $cachePath;

    public function setUp()
    {
        $this->cachePath = __DIR__ . '/../../cache';
        $this->deleteDir($this->cachePath);
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
        $configWithCache = new YamlConfig(__DIR__ . '/../../Fixtures/hateoas.yml', $this->cachePath);
        $this->assertTrue(file_exists($this->cachePath.'/3e401f87a385e0649a7388abd55c2b9b.yml.cache'));
        $this->assertSame($config->getResourceDefinitions(), $configWithCache->getResourceDefinitions());
        $this->assertSame($config->getCollectionDefinitions(), $configWithCache->getCollectionDefinitions());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testParseInvalidFile()
    {
        $config = new YamlConfig(__DIR__ . '/../../Fixtures/hateoas-404.yml');
    }

    public function tearDown()
    {
        $this->deleteDir($this->cachePath);
    }

    private function deleteDir($dirPath)
    {
        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dirPath, \FilesystemIterator::SKIP_DOTS), \RecursiveIteratorIterator::CHILD_FIRST) as $path) {
            $path->isFile() ? unlink($path->getPathname()) : rmdir($path->getPathname());
        }
    }
}
