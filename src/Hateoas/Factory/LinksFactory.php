<?php

namespace Hateoas\Factory;

use Hateoas\Configuration\RelationsRepository;
use Hateoas\Model\Link;
use Hateoas\Serializer\ExclusionManager;
use JMS\Serializer\SerializationContext;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class LinksFactory
{
    /**
     * @var RelationsRepository
     */
    private $relationsRepository;

    /**
     * @var LinkFactory
     */
    private $linkFactory;

    /**
     * @var ExclusionManager
     */
    private $exclusionManager;

    /**
     * @param RelationsRepository $relationsRepository
     * @param LinkFactory         $linkFactory
     * @param ExclusionManager    $exclusionManager
     */
    public function __construct(RelationsRepository $relationsRepository, LinkFactory $linkFactory, ExclusionManager $exclusionManager)
    {
        $this->relationsRepository = $relationsRepository;
        $this->linkFactory         = $linkFactory;
        $this->exclusionManager    = $exclusionManager;
    }

    /**
     * @param object               $object
     * @param SerializationContext $context
     *
     * @return Link[]
     */
    public function create($object, SerializationContext $context)
    {
        $links = array();
        foreach ($this->relationsRepository->getRelations($object) as $relation) {
            if ($this->exclusionManager->shouldSkipLink($object, $relation, $context)) {
                continue;
            }

            $links[] = $this->linkFactory->createLink($object, $relation);
        }

        return $links;
    }
}
