<?php

namespace Hateoas\Helper;

use Hateoas\Configuration\RelationsRepository;
use Hateoas\Configuration\Relation;
use Hateoas\Configuration\Route;
use Hateoas\Factory\LinkFactory;
use JMS\Serializer\SerializationContext;
use Metadata\MetadataFactoryInterface;

/**
 * @author William Durand <william.durand1@gmail.com>
 */
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

    /**
     * @param object  $object
     * @param string  $rel
     * @param boolean $absolute
     *
     * @return string
     */
    public function getLinkHref($object, $rel, $absolute = false, SerializationContext $context = null)
    {
        $context = $context ?? SerializationContext::create();

        if (null !== ($classMetadata = $this->metadataFactory->getMetadataForClass(get_class($object)))) {
            foreach ($classMetadata->getRelations() as $relation) {
                if ($rel === $relation->getName()) {
                    $relation = $this->patchAbsolute($relation, $absolute);

                    if (null !== $link = $this->linkFactory->createLink($object, $relation, $context)) {
                        return $link->getHref();
                    }
                }
            }

        }

        return null;
    }

    private function patchAbsolute(Relation $relation, $absolute)
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
