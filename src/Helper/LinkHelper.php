<?php

declare(strict_types=1);

namespace Hateoas\Helper;

use Hateoas\Configuration\Relation;
use Hateoas\Configuration\Route;
use Hateoas\Factory\LinkFactory;
use Hateoas\Util\ClassUtils;
use JMS\Serializer\SerializationContext;
use Metadata\MetadataFactoryInterface;

class LinkHelper
{
    /**
     * @var LinkFactory
     */
    private $linkFactory;

    /**
     * @var MetadataFactoryInterface
     */
    private $metadataFactory;

    public function __construct(LinkFactory $linkFactory, MetadataFactoryInterface $metadataFactory)
    {
        $this->linkFactory         = $linkFactory;
        $this->metadataFactory = $metadataFactory;
    }

    public function getLinkHref(object $object, string $rel, bool $absolute = false, ?SerializationContext $context = null): string
    {
        $context = $context ?? SerializationContext::create();

        if (null !== ($classMetadata = $this->metadataFactory->getMetadataForClass(ClassUtils::getClass($object)))) {
            foreach ($classMetadata->getRelations() as $relation) {
                if ($rel === $relation->getName()) {
                    $relation = $this->patchAbsolute($relation, $absolute);

                    if (null !== $link = $this->linkFactory->createLink($object, $relation, $context)) {
                        return $link->getHref();
                    }
                }
            }
        }

        throw new \RuntimeException(sprintf('Can not find the relation "%s" for the "%s" class', $rel, ClassUtils::getClass($object)));
    }

    /**
     * @param mixed $absolute
     */
    private function patchAbsolute(Relation $relation, $absolute): Relation
    {
        $href = $relation->getHref();

        if ($href instanceof Route) {
            $href = new Route(
                $href->getName(),
                $href->getParameters(),
                $absolute,
                $href->getGenerator()
            );
        }

        return new Relation(
            $relation->getName(),
            $href,
            $relation->getEmbedded(),
            $relation->getAttributes(),
            $relation->getExclusion()
        );
    }
}
