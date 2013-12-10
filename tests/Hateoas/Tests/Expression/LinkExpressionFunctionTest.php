<?php

namespace Hateoas\Tests\Expression;

use Hateoas\HateoasBuilder;
use Hateoas\UrlGenerator\CallableUrlGenerator;
use Hateoas\Tests\Fixtures\Post;
use Hateoas\Tests\Fixtures\Will;

class LinkExpressionFunctionTest extends \PHPUnit_Framework_TestCase
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

    public function testGetLinkHrefWithFunctionExpression()
    {
        $this->assertEquals('{"id":123,"post":{"id":456,"_links":{"self":{"href":"\/posts\/456"}}},"_links":{"self":{"href":"\/users\/123"},"post":{"href":"http:\/\/example.com\/posts\/456"}}}', $this->hateoas->serialize(new Will(123, new Post(456)), 'json'));
    }
}
