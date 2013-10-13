<?php

namespace Hateoas\Tests\UrlGenerator;

use Hateoas\Tests\TestCase;
use Hateoas\UrlGenerator\CallableUrlGenerator;

class CallableUrlGeneratorTest extends TestCase
{
    public function test()
    {
        $expectedName = 'user_get';
        $expectedParameters = array('id' => 42);
        $expectedAbsolute = true;
        $expectedResult = '/users/42';

        $test = $this;
        $callable = function ($name, $parameters, $absolute) use ($expectedName, $expectedParameters, $expectedResult, $expectedAbsolute, $test) {
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
        $urlGenerator = new CallableUrlGenerator($callable);

        $this
            ->string($urlGenerator->generate($expectedName, $expectedParameters, $expectedAbsolute))
                ->isEqualTo($expectedResult)
        ;
    }
}
