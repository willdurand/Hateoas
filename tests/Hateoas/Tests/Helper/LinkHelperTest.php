<?php

namespace Hateoas\Tests\Helper;

use Hateoas\Configuration\Metadata\ClassMetadata;
use Hateoas\Configuration\Relation;
use Hateoas\Configuration\Route;
use Hateoas\HateoasBuilder;
use Hateoas\Helper\LinkHelper;
use Hateoas\Tests\TestCase;
use Hateoas\UrlGenerator\CallableUrlGenerator;
use Hateoas\Tests\Fixtures\Will;
use Metadata\MetadataFactoryInterface;

class LinkHelperTest extends TestCase
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

    public function testGetLinkHref()
    {
        $linkHelper = new LinkHelper($this->getLinkFactoryMock(), $this->getMetadataFactoryMock());

        $this->assertEquals(
            'http://example.com/me',
            $linkHelper->getLinkHref(new Will(123), 'self')
        );
    }

    public function testGetLinkHrefWithRoute()
    {
        $linkHelper = new LinkHelper($this->getLinkFactoryMock(), $this->getMetadataFactoryMock());

        $this->assertEquals(
            'my-self-route',
            $linkHelper->getLinkHref(new Will(123), 'self-route')->getName()
        );
    }

    public function testGetLinkHrefReturnsNullIfRelNotFound()
    {
        $linkHelper = new LinkHelper($this->getLinkFactoryMock($this->never()), $this->getMetadataFactoryMock());

        $this->assertNull($linkHelper->getLinkHref(new Will(123), 'unknown-rel'));
    }

    /**
     * @return MetadataFactoryInterface
     */
    private function getMetadataFactoryMock()
    {
        $metadataMock = $this->getMockBuilder(ClassMetadata::class)
            ->disableOriginalConstructor()
            ->getMock();

        $metadataMock
            ->expects($this->once())
            ->method('getRelations')
            ->will($this->returnValue(array(
                new Relation('self', 'http://example.com/me'),
                new Relation('self-route', new Route('my-self-route')),
            )));

        $metadataFactoryMock = $this->getMockBuilder(MetadataFactoryInterface::class)
            ->getMock();

        $metadataFactoryMock
            ->expects($this->once())
            ->method('getMetadataForClass')
            ->will($this->returnValue($metadataMock));

        return $metadataFactoryMock;
    }

    /**
     * @param \PHPUnit_Framework_MockObject_Matcher_InvokedCount $expects
     *
     * @return \Hateoas\Factory\LinkFactory
     */
    private function getLinkFactoryMock($expects = null)
    {
        if (null === $expects) {
            $expects = $this->once();
        }

        $linkFactoryMock = $this->getMockBuilder('Hateoas\Factory\LinkFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $linkFactoryMock
            ->expects($expects)
            ->method('createLink')
            ->will($this->returnArgument(1));

        return $linkFactoryMock;
    }
}
