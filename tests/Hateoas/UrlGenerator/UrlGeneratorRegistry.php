<?php

namespace tests\Hateoas\UrlGenerator;

use tests\TestCase;
use Hateoas\UrlGenerator\UrlGeneratorRegistry as TestedUrlGeneratorRegistry;

class UrlGeneratorRegistry extends TestCase
{
    public function test()
    {
        $defaultUrlGenerator = $this->mockUrlGenerator();
        $registry = new TestedUrlGeneratorRegistry($defaultUrlGenerator);

        $this
            ->object($registry->get(TestedUrlGeneratorRegistry::DEFAULT_URL_GENERATOR_KEY))
                ->isEqualTo($defaultUrlGenerator)
            ->object($registry->get())
                ->isEqualTo($defaultUrlGenerator)
            ->exception(function () use ($registry) {
                $registry->get('foo');
            })
                ->isInstanceOf('InvalidArgumentException')
                    ->hasMessage('The "foo" url generator is not set. Available url generators are default.')

            ->when($registry->set('foo', $fooUrlGenerator = $this->mockUrlGenerator()))
            ->object($registry->get('foo'))
                ->isEqualTo($fooUrlGenerator)
        ;
    }

    private function mockUrlGenerator()
    {
        return new \mock\Hateoas\UrlGenerator\UrlGeneratorInterface();
    }
}
