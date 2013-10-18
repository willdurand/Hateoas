<?php

namespace Hateoas\Tests\UrlGenerator;

use Hateoas\Tests\TestCase;
use Hateoas\UrlGenerator\SymfonyUrlGenerator;

class SymfonyUrlGeneratorTest extends TestCase
{
    public function test()
    {
        $name           = 'user_get';
        $parameters     = array('id' => 42);
        $absolute       = true;
        $expectedResult = '/users/42';

        $symfonyUrlGeneratorProphecy = $this->prophesize('Symfony\Component\Routing\Generator\UrlGeneratorInterface');
        $symfonyUrlGeneratorProphecy
            ->generate($name, $parameters, $absolute)
            ->willReturn($expectedResult)
        ;

        $urlGenerator = new SymfonyUrlGenerator($symfonyUrlGeneratorProphecy->reveal());

        $this
            ->string($urlGenerator->generate($name, $parameters, $absolute))
                ->isEqualTo($expectedResult)
        ;
    }
}
