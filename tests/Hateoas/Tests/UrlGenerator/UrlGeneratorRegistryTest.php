<?php

namespace Hateoas\Tests\UrlGenerator;

use Hateoas\Tests\TestCase;
use Hateoas\UrlGenerator\UrlGeneratorRegistry;

class UrlGeneratorRegistryTest extends TestCase
{
    public function test()
    {
        $defaultUrlGenerator = $this->mockUrlGenerator();
        $registry = new UrlGeneratorRegistry($defaultUrlGenerator);

        $this
            ->object($registry->get(UrlGeneratorRegistry::DEFAULT_URL_GENERATOR_KEY))
                ->isEqualTo($defaultUrlGenerator)
            ->object($registry->get())
                ->isEqualTo($defaultUrlGenerator)
            ->exception(function () use ($registry) {
                $registry->get('foo');
            })
                ->isInstanceOf('InvalidArgumentException')
                    ->hasMessage('The "foo" url generator is not set. Available url generators are: default.')

            ->when($registry->set('foo', $fooUrlGenerator = $this->mockUrlGenerator()))
            ->object($registry->get('foo'))
                ->isEqualTo($fooUrlGenerator)
        ;
    }

    private function mockUrlGenerator()
    {
        return $this->prophesize('Hateoas\UrlGenerator\UrlGeneratorInterface')->reveal();
    }
}
