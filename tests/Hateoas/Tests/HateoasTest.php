<?php

namespace Hateoas\Tests;

use Hateoas\HateoasBuilder;
use Hateoas\UrlGenerator\CallableUrlGenerator;
use Hateoas\Tests\Fixtures\Will;

class HateoasTest extends \PHPUnit_Framework_TestCase
{
    private $hateoas;

    protected function setUp()
    {
        $this->hateoas = HateoasBuilder::create()
            ->setUrlGenerator(null, new CallableUrlGenerator(function ($name, $parameters, $absolute) {
                if ($name === 'user_get') {
                    return sprintf(
                        '%s%s',
                        $absolute ? 'http://example.com' : '',
                        strtr('/users/id', $parameters)
                    );
                }

                if ($name === 'post_get') {
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

    public function testGetLinkHrefUrlWithUnknownRelShouldReturnNull()
    {
        $this->assertNull($this->hateoas->getLinkHelper()->getLinkHref(new Will(123), 'unknown-rel'));
        $this->assertNull($this->hateoas->getLinkHelper()->getLinkHref(new Will(123), 'unknown-rel', true));
    }

    public function testGetLinkHrefUrl()
    {
        $this->assertEquals('/users/123', $this->hateoas->getLinkHelper()->getLinkHref(new Will(123), 'self'));
        $this->assertEquals('/users/123', $this->hateoas->getLinkHelper()->getLinkHref(new Will(123), 'self', false));
    }

    public function testGetLinkHrefUrlWithAbsoluteTrue()
    {
        $this->assertEquals('http://example.com/users/123', $this->hateoas->getLinkHelper()->getLinkHref(new Will(123), 'self', true));
    }
}
