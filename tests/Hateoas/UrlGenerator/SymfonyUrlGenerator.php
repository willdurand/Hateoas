<?php

namespace tests\Hateoas\UrlGenerator;

use tests\TestCase;
use Hateoas\UrlGenerator\SymfonyUrlGenerator as TestedSymfonyUrlGenerator;

class SymfonyUrlGenerator extends TestCase
{
    public function test()
    {
        $expectedName       = 'user_get';
        $expectedParameters = array('id' => 42);
        $expectedAbsolute   = true;
        $expectedResult     = '/users/42';

        $test = $this;
        $symfonyUrlGenerator = new \mock\Symfony\Component\Routing\Generator\UrlGeneratorInterface();
        $symfonyUrlGenerator->getMockController()->generate = function ($name, $parameters, $absolute)
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

        $urlGenerator = new TestedSymfonyUrlGenerator($symfonyUrlGenerator);

        $this
            ->string($urlGenerator->generate($expectedName, $expectedParameters, $expectedAbsolute))
                ->isEqualTo($expectedResult)
        ;
    }
}
