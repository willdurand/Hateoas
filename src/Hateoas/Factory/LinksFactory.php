<?php

namespace Hateoas\Factory;

use Hateoas\Model\Link;
use Hateoas\Serializer\ExclusionManager;
use JMS\Serializer\SerializationContext;
use Metadata\MetadataFactoryInterface;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class LinksFactory
{

    /**
     * @var LinkFactory
     */
    private $linkFactory;

    /**
     * @var ExclusionManager
     */
    private $exclusionManager;

    /**
     * @var MetadataFactoryInterface
     */
    private $metadataFactory;

    public function __construct(
        MetadataFactoryInterface $metadataFactory,
        LinkFactory $linkFactory,
        ExclusionManager $exclusionManager
    ) {
        $this->linkFactory = $linkFactory;
        $this->exclusionManager = $exclusionManager;
        $this->metadataFactory = $metadataFactory;
    }

    /**
     * @param object $object
     * @param SerializationContext $context
     *
     * @return Link[]
     */
    public function create($object, SerializationContext $context)
    {
        $links = array();
        if (null !== ($classMetadata = $this->metadataFactory->getMetadataForClass(get_class($object)))) {

            foreach ($classMetadata->getRelations() as $relation) {
                if ($this->exclusionManager->shouldSkipLink($object, $relation, $context)) {
                    continue;
                }

                $links[] = $this->linkFactory->createLink($object, $relation, $context);
            }
        }

        return $links;
    }
}
