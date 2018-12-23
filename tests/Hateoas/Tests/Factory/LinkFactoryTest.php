<?php

declare(strict_types=1);

namespace Hateoas\Tests\Factory;

use Hateoas\Configuration\Relation;
use Hateoas\Configuration\Route;
use Hateoas\Factory\LinkFactory;
use Hateoas\Tests\TestCase;
use Hateoas\UrlGenerator\CallableUrlGenerator;
use Hateoas\UrlGenerator\UrlGeneratorRegistry;
use JMS\Serializer\Expression\ExpressionEvaluator;
use JMS\Serializer\SerializationContext;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class LinkFactoryTest extends TestCase
{
    protected function expr($expr)
    {
        $expressionEvaluator = new ExpressionEvaluator(new ExpressionLanguage());

        return $expressionEvaluator->parse($expr, ['object']);
    }

    public function test()
    {
        $context = SerializationContext::create();
        $link = $this->createLinkFactory()->createLink(
            new TestedObject(),
            new Relation('foo', '/bar', null, ['templated' => false]),
            $context
        );

        $this->assertInstanceOf('Hateoas\Model\Link', $link);
        $this->assertSame('foo', $link->getRel());
        $this->assertSame('/bar', $link->getHref());
        $this->assertSame(['templated' => false], $link->getAttributes());
    }

    public function testRoute()
    {
        $context = SerializationContext::create();
        $link = $this->createLinkFactory()->createLink(
            new TestedObject(),
            new Relation('foo', new Route('/route', ['foo' => 'bar'])),
            $context
        );

        $this->assertInstanceOf('Hateoas\Model\Link', $link);
        $this->assertSame('foo', $link->getRel());
        $this->assertSame('/route?foo=bar', $link->getHref());
    }

    public function testExpressions()
    {
        $context = SerializationContext::create();
        $link = $this->createLinkFactory()->createLink(
            new TestedObject(),
            new Relation(
                'rel',
                $this->expr('object.getUrl()'),
                null,
                ['tested-rel' => $this->expr('object.getUrl()')]
            ),
            $context
        );

        $this->assertInstanceOf('Hateoas\Model\Link', $link);
        $this->assertSame('rel', $link->getRel());
        $this->assertSame('/tested-url', $link->getHref());
        $this->assertSame(['tested-rel' => '/tested-url'], $link->getAttributes());
    }

    public function testParametersExpression()
    {
        $context = SerializationContext::create();
        $link = $this->createLinkFactory()->createLink(
            new TestedObject(),
            new Relation('foo', new Route('/route', $this->expr('object.getParameters()'))),
            $context
        );

        $this->assertInstanceOf('Hateoas\Model\Link', $link);
        $this->assertSame('foo', $link->getRel());
        $this->assertSame('/route?a=b', $link->getHref());
    }

    public function testParametersDeepArrayExpression()
    {
        $context = SerializationContext::create();
        $link = $this->createLinkFactory()->createLink(
            new TestedObject(),
            new Relation(
                'foo',
                new Route(
                    '/route',
                    [
                        'param' => [$this->expr('object.getRel()')],
                    ]
                )
            ),
            $context
        );

        $this->assertInstanceOf('Hateoas\Model\Link', $link);
        $this->assertSame('foo', $link->getRel());
        $this->assertSame('/route?param%5B0%5D=tested-rel', $link->getHref());
    }

    public function testRouteRequiresGenerator()
    {
        $expressionEvaluator = new ExpressionEvaluator(new ExpressionLanguage());
        $urlGeneratorRegistry = new UrlGeneratorRegistry();

        $linkFactory = new LinkFactory($urlGeneratorRegistry, $expressionEvaluator);

        $this->expectException('RuntimeException');
        $this->expectExceptionMessage('You cannot use a route without an url generator.');

        $context = SerializationContext::create();
        $linkFactory->createLink(
            new TestedObject(),
            new Relation('foo', new Route('/route', ['foo' => 'bar'])),
            $context
        );
    }

    public function testRouteParamatersNotArray()
    {
        $linkFactory = $this->createLinkFactory();

        $this->expectException('RuntimeException');
        $this->expectExceptionMessage('The route parameters should be an array, string given. Maybe you forgot to wrap the expression in expr(...).');

        $context = SerializationContext::create();
        $linkFactory->createLink(
            new TestedObject(),
            new Relation('foo', new Route('/route', 'yolo')),
            $context
        );
    }

    private function createLinkFactory()
    {
        $defaultUrlGenerator = new CallableUrlGenerator(function ($route, $parameters) {
            return $route . '?' . http_build_query($parameters);
        });
        $expressionEvaluator = new ExpressionEvaluator(new ExpressionLanguage());
        $urlGeneratorRegistry = new UrlGeneratorRegistry($defaultUrlGenerator);

        return new LinkFactory($urlGeneratorRegistry, $expressionEvaluator);
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
        return ['a' => 'b'];
    }
}
