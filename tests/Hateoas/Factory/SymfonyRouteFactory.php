<?php

namespace tests\Hateoas\Factory;

use tests\TestCase;
use Hateoas\Factory\SymfonyRouteFactory as TestedSymfonyRouteFactory;

class SymfonyRouteFactory extends TestCase
{
    public function test()
    {
        $expectedName       = 'user_get';
        $expectedParameters = array('id' => 42);
        $expectedAbsolute   = true;
        $expectedResult     = '/users/42';

        $test = $this;
        $urlGenerator = new \mock\Symfony\Component\Routing\Generator\UrlGeneratorInterface();
        $urlGenerator->getMockController()->generate = function ($name, $parameters, $absolute)
            use ($expectedName, $expectedParameters, $expectedResult, $expectedAbsolute, $test) {
                $test
                ->string($name)
                    ->isEqualTo($expectedName)
                ->array($parameters)
                    ->isEqualTo($expectedParameters)
                ->boolean($absolute)
                    ->isEqualTo($expectedAbsolute)
            ;

            return $expectedResult;
        };

        $routeFactory = new TestedSymfonyRouteFactory($urlGenerator);

        $this
            ->string($routeFactory->create($expectedName, $expectedParameters, $expectedAbsolute))
                ->isEqualTo($expectedResult)
        ;
    }
}
