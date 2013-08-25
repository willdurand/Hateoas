<?php

namespace tests\Hateoas\Handler;

use tests\TestCase;
use Hateoas\Handler\SymfonyRequestHandler as TestedSymfonyRequestHandler;

class SymfonyRequestHandler extends TestCase
{
    public function testNoRequest()
    {
        $requestHandler = new TestedSymfonyRequestHandler();

        $this
            ->exception(function () use ($requestHandler) {
                $requestHandler->transform('', null);
            })
                ->isInstanceOf('RuntimeException')
                ->hasMessage('The request was not set on the SymfonyRequestHandler.')
        ;
    }

    public function testInvalidValue()
    {
        $request = new \mock\Symfony\Component\HttpFoundation\Request;
        $requestHandler = new TestedSymfonyRequestHandler();
        $requestHandler->setRequest($request);

        $this
            ->variable($requestHandler->transform('', null))
                ->isNull()
        ;
    }

    public function testInvalidProperty()
    {
        $request = new \mock\Symfony\Component\HttpFoundation\Request;
        $requestHandler = new TestedSymfonyRequestHandler();
        $requestHandler->setRequest($request);

        $this
            ->exception(function () use ($requestHandler) {
                $requestHandler->transform('fake.something', null);
            })
                ->isInstanceOf('InvalidArgumentException')
                ->hasMessage('The property "fake" is not supported on the Request.')
        ;
    }

    public function testMethodsAndProperties()
    {
        $request = new \mock\Symfony\Component\HttpFoundation\Request();
        $request->query->set('page', $page = 5);
        $request->request->set('age', $age = 2044);
        $request->attributes->set('_route', $route = 'user_all');
        $request->cookies->set('PHPSESSID', $sessId = 'abcd');
        $request->server->set('SERVER_NAME', $serverName = 'hateoas');
        $request->headers->set('X-Hello', $hello = 'adrien');
        $request->setRequestFormat($requestFormat = 'application/json');
        $requestHandler = new TestedSymfonyRequestHandler();
        $requestHandler->setRequest($request);

        $this
            ->variable($requestHandler->transform('query.page', null))
                ->isEqualTo($page)
            ->variable($requestHandler->transform('request.age', null))
                ->isEqualTo($age)
            ->variable($requestHandler->transform('attributes._route', null))
                ->isEqualTo($route)
            ->variable($requestHandler->transform('cookies.PHPSESSID', null))
                ->isEqualTo($sessId)
            ->variable($requestHandler->transform('server.SERVER_NAME', null))
                ->isEqualTo($serverName)
            ->variable($requestHandler->transform('headers.X-Hello', null))
                ->isEqualTo($hello)
            ->variable($requestHandler->transform('requestFormat', null))
                ->isEqualTo($requestFormat)
        ;
    }
}
