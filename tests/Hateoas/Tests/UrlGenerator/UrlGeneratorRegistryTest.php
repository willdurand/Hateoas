<?php

declare(strict_types=1);

namespace Hateoas\Tests\UrlGenerator;

use Hateoas\Tests\TestCase;
use Hateoas\UrlGenerator\UrlGeneratorRegistry;
use Prophecy\PhpUnit\ProphecyTrait;

class UrlGeneratorRegistryTest extends TestCase
{
    use ProphecyTrait;

    public function test()
    {
        $defaultUrlGenerator = $this->mockUrlGenerator();
        $registry = new UrlGeneratorRegistry($defaultUrlGenerator);

        $this->assertSame($defaultUrlGenerator, $registry->get(UrlGeneratorRegistry::DEFAULT_URL_GENERATOR_KEY));
        $this->assertSame($defaultUrlGenerator, $registry->get());

        $exception = null;
        try {
            $registry->get('foo');
        } catch (\Throwable $e) {
            $exception = $e;
        }

        $this->assertInstanceOf('InvalidArgumentException', $exception);
        $this->assertSame(
            'The "foo" url generator is not set. Available url generators are: default.',
            $exception->getMessage()
        );

        $registry->set('foo', $fooUrlGenerator = $this->mockUrlGenerator());
        $this->assertSame($fooUrlGenerator, $registry->get('foo'));
    }

    private function mockUrlGenerator()
    {
        return $this->prophesize('Hateoas\UrlGenerator\UrlGeneratorInterface')->reveal();
    }
}
