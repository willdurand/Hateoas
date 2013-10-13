<?php

namespace Hateoas\Tests\Factory;

use Hateoas\Configuration\Relation;
use Hateoas\Configuration\Route;
use Hateoas\Expression\ExpressionEvaluator;
use Hateoas\Factory\LinkFactory;
use Hateoas\Tests\TestCase;
use Hateoas\UrlGenerator\CallableUrlGenerator;
use Hateoas\UrlGenerator\UrlGeneratorRegistry;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class LinkFactoryTest extends TestCase
{
    public function test()
    {
        $link = $this->createLinkFactory()->createLink(
            new TestedObject(),
            new Relation('foo', '/bar', null, array('templated' => false))
        );

        $this
            ->object($link)
                ->isInstanceOf('Hateoas\Model\Link')
            ->string($link->getRel())
                ->isEqualTo('foo')
            ->string($link->getHref())
                ->isEqualTo('/bar')
            ->array($link->getAttributes())
                ->isEqualTo(array('templated' => false))
        ;
    }

    public function testRoute()
    {
        $link = $this->createLinkFactory()->createLink(
            new TestedObject(),
            new Relation('foo', new Route('/route', array('foo' => 'bar')))
        );

        $this
            ->object($link)
                ->isInstanceOf('Hateoas\Model\Link')
            ->string($link->getRel())
                ->isEqualTo('foo')
            ->string($link->getHref())
                ->isEqualTo('/route?foo=bar')
        ;
    }

    public function testExpressions()
    {
        $link = $this->createLinkFactory()->createLink(
            new TestedObject(),
            new Relation('expr(object.getRel())', 'expr(object.getUrl())', null, array('expr(object.getRel())' => 'expr(object.getUrl())'))
        );

        $this
            ->object($link)
                ->isInstanceOf('Hateoas\Model\Link')
            ->string($link->getRel())
                ->isEqualTo('tested-rel')
            ->string($link->getHref())
                ->isEqualTo('/tested-url')
            ->array($link->getAttributes())
                ->isEqualTo(array('tested-rel' => '/tested-url'))
        ;
    }

    public function testParametersExpression()
    {
        $link = $this->createLinkFactory()->createLink(
            new TestedObject(),
            new Relation('foo', new Route('/route', 'expr(object.getParameters())'))
        );

        $this
            ->object($link)
                ->isInstanceOf('Hateoas\Model\Link')
            ->string($link->getRel())
                ->isEqualTo('foo')
            ->string($link->getHref())
                ->isEqualTo('/route?a=b')
        ;
    }

    public function testParametersDeepArrayExpression()
    {
        $link = $this->createLinkFactory()->createLink(
            new TestedObject(),
            new Relation(
                'foo',
                new Route(
                    '/route',
                    array(
                        'expr(object.getRel())' => array('expr(object.getRel())')
                    )
                )
            )
        );

        $this
            ->object($link)
                ->isInstanceOf('Hateoas\Model\Link')
            ->string($link->getRel())
                ->isEqualTo('foo')
            ->string($link->getHref())
                ->isEqualTo('/route?tested-rel%5B0%5D=tested-rel')
        ;
    }

    public function testRouteRequiresGenerator()
    {
        $expressionEvaluator = new ExpressionEvaluator(new ExpressionLanguage());
        $urlGeneratorRegistry = new UrlGeneratorRegistry();

        $linkFactory = new LinkFactory($expressionEvaluator, $urlGeneratorRegistry);

        $this
            ->exception(function () use ($linkFactory) {
                $linkFactory->createLink(
                    new TestedObject(),
                    new Relation('foo', new Route('/route', array('foo' => 'bar')))
                );
            })
                ->isInstanceOf('RuntimeException')
                    ->hasMessage('You cannot use a route without an url generator.')
        ;
    }

    public function testRouteParamatersNotArray()
    {
        $linkFactory = $this->createLinkFactory();

        $this
            ->exception(function () use ($linkFactory) {
                $linkFactory->createLink(
                    new TestedObject(),
                    new Relation('foo', new Route('/route', 'yolo'))
                );
            })
                ->isInstanceOf('RuntimeException')
                    ->hasMessage('The route parameters should be an array, string given. Maybe you forgot to wrap the expression in expr(...).')
        ;
    }

    private function createLinkFactory()
    {
        $defaultUrlGenerator = new CallableUrlGenerator(function ($route, $parameters) {
            return $route . '?' . http_build_query($parameters);
        });
        $expressionEvaluator = new ExpressionEvaluator(new ExpressionLanguage());
        $urlGeneratorRegistry = new UrlGeneratorRegistry($defaultUrlGenerator);

        return new LinkFactory($expressionEvaluator, $urlGeneratorRegistry);
    }
}

class TestedObject
{
    public function getRel()
    {
        return 'tested-rel';
    }

    public function getUrl()
    {
        return '/tested-url';
    }

    public function getParameters()
    {
        return array(
            'a' => 'b',
        );
    }
}
