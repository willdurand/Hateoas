<?php

declare(strict_types=1);

namespace Hateoas\Tests\Helper;

use Hateoas\Configuration\Metadata\ClassMetadata;
use Hateoas\Configuration\Relation;
use Hateoas\Configuration\Route;
use Hateoas\Factory\LinkFactory;
use Hateoas\HateoasBuilder;
use Hateoas\Helper\LinkHelper;
use Hateoas\Model\Link;
use Hateoas\Tests\Fixtures\Will;
use Hateoas\Tests\TestCase;
use Hateoas\UrlGenerator\CallableUrlGenerator;
use Metadata\MetadataFactoryInterface;

class LinkHelperTest extends TestCase
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

    public function testGetLinkHref()
    {
        $linkHelper = new LinkHelper($this->getLinkFactoryMock(), $this->getMetadataFactoryMock());

        $this->assertEquals(
            'http://example.com/self',
            $linkHelper->getLinkHref(new Will(123), 'self')
        );
    }

    public function testGetLinkHrefReturnsNullIfRelNotFound()
    {
        $linkHelper = new LinkHelper($this->getLinkFactoryMock($this->never()), $this->getMetadataFactoryMock());

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Can not find the relation "unknown-rel" for the "Hateoas\Tests\Fixtures\Will" class');

        $linkHelper->getLinkHref(new Will(123), 'unknown-rel');
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
            ->will($this->returnValue([
                new Relation('self', 'http://example.com/me'),
                new Relation('self-route', new Route('my-self-route')),
            ]));

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
     * @return LinkFactory
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
            ->will($this->returnCallback(function ($obj, Relation $relation) {
                return new Link($relation->getName(), 'http://example.com/' . $relation->getName());
            }));

        return $linkFactoryMock;
    }
}
