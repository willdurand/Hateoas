<?php

namespace tests\Hateoas\Factory;

use tests\Test;
use Hateoas\Factory\CallableRouteFactory as TestedCallableRouteFactory;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class CallableRouteFactory extends Test
{
    public function test()
    {
        $expectedName = 'user_get';
        $expectedParameters = array('id' => 42);
        $expectedResult = '/users/42';

        $test = $this;
        $callable = function ($name, $parameters) use ($expectedName, $expectedParameters, $expectedResult, $test) {
            $test
                ->string($name)
                    ->isEqualTo($expectedName)
                ->array($parameters)
                    ->isEqualTo($expectedParameters)
            ;
            return $expectedResult;
        };
        $routeFactory = new TestedCallableRouteFactory($callable);

        $this
            ->string($routeFactory->create($expectedName, $expectedParameters))
                ->isEqualTo($expectedResult)
        ;
    }
}