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

        $this->assertInstanceOf('Hateoas\Model\Link', $link);
        $this->assertSame('foo', $link->getRel());
        $this->assertSame('/bar', $link->getHref());
        $this->assertSame(['templated' => false], $link->getAttributes());
    }

    public function testRoute()
    {
        $link = $this->createLinkFactory()->createLink(
            new TestedObject(),
            new Relation('foo', new Route('/route', array('foo' => 'bar')))
        );

        $this->assertInstanceOf('Hateoas\Model\Link', $link);
        $this->assertSame('foo', $link->getRel());
        $this->assertSame('/route?foo=bar', $link->getHref());
    }

    public function testExpressions()
    {
        $link = $this->createLinkFactory()->createLink(
            new TestedObject(),
            new Relation('expr(object.getRel())', 'expr(object.getUrl())', null, array('expr(object.getRel())' => 'expr(object.getUrl())'))
        );

        $this->assertInstanceOf('Hateoas\Model\Link', $link);
        $this->assertSame('tested-rel', $link->getRel());
        $this->assertSame('/tested-url', $link->getHref());
        $this->assertSame(['tested-rel' => '/tested-url'], $link->getAttributes());
    }

    public function testParametersExpression()
    {
        $link = $this->createLinkFactory()->createLink(
            new TestedObject(),
            new Relation('foo', new Route('/route', 'expr(object.getParameters())'))
        );

        $this->assertInstanceOf('Hateoas\Model\Link', $link);
        $this->assertSame('foo', $link->getRel());
        $this->assertSame('/route?a=b', $link->getHref());
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

        $this->assertInstanceOf('Hateoas\Model\Link', $link);
        $this->assertSame('foo', $link->getRel());
        $this->assertSame('/route?tested-rel%5B0%5D=tested-rel', $link->getHref());
    }

    public function testRouteRequiresGenerator()
    {
        $expressionEvaluator = new ExpressionEvaluator(new ExpressionLanguage());
        $urlGeneratorRegistry = new UrlGeneratorRegistry();

        $linkFactory = new LinkFactory($expressionEvaluator, $urlGeneratorRegistry);

        $this->setExpectedException('RuntimeException', 'You cannot use a route without an url generator.');

        $linkFactory->createLink(
            new TestedObject(),
            new Relation('foo', new Route('/route', array('foo' => 'bar')))
        );
    }

    public function testRouteParamatersNotArray()
    {
        $linkFactory = $this->createLinkFactory();

        $this->setExpectedException(
            'RuntimeException',
            'The route parameters should be an array, string given. Maybe you forgot to wrap the expression in expr(...).'
        );

        $linkFactory->createLink(
            new TestedObject(),
            new Relation('foo', new Route('/route', 'yolo'))
        );
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
