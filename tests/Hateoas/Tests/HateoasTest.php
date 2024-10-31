<?php

declare(strict_types=1);

namespace Hateoas\Tests;

use Doctrine\Common\Annotations\AnnotationReader;
use Hateoas\HateoasBuilder;
use Hateoas\Tests\Fixtures\Attribute;
use Hateoas\Tests\Fixtures\Will;
use Hateoas\UrlGenerator\CallableUrlGenerator;

class HateoasTest extends TestCase
{
    private $hateoas;

    protected function setUp(): void
    {
        $this->hateoas = HateoasBuilder::create()
            ->setUrlGenerator(null, new CallableUrlGenerator(function ($name, $parameters, $absolute) {
                if ('user_get' === $name) {
                    return sprintf(
                        '%s%s',
                        $absolute ? 'http://example.com' : '',
                        strtr('/users/id', $parameters)
                    );
                }

                if ('post_get' === $name) {
                    return sprintf(
                        '%s%s',
                        $absolute ? 'http://example.com' : '',
                        strtr('/posts/id', $parameters)
                    );
                }

                throw new \RuntimeException('Cannot generate URL');
            }))
            ->build();
    }

    public function testGetLinkHrefUrlWithUnknownRelThrowsException()
    {
        if (class_exists(AnnotationReader::class)) {
            $className = Will::class;
        } else {
            $className = Attribute\Will::class;
        }

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(sprintf('Can not find the relation "unknown-rel" for the "%s" class', $className));
        $this->assertNull($this->hateoas->getLinkHelper()->getLinkHref(new $className(123), 'unknown-rel'));
        $this->assertNull($this->hateoas->getLinkHelper()->getLinkHref(new $className(123), 'unknown-rel', true));
    }

    public function testGetLinkHrefUrl()
    {
        if (class_exists(AnnotationReader::class)) {
            $className = Will::class;
        } else {
            $className = Attribute\Will::class;
        }

        $this->assertEquals('/users/123', $this->hateoas->getLinkHelper()->getLinkHref(new $className(123), 'self'));
        $this->assertEquals('/users/123', $this->hateoas->getLinkHelper()->getLinkHref(new $className(123), 'self', false));
    }

    public function testGetLinkHrefUrlWithAbsoluteTrue()
    {
        if (class_exists(AnnotationReader::class)) {
            $className = Will::class;
        } else {
            $className = Attribute\Will::class;
        }

        $this->assertEquals('http://example.com/users/123', $this->hateoas->getLinkHelper()->getLinkHref(new $className(123), 'self', true));
    }
}
