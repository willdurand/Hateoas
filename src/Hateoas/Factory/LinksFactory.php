<?php

namespace Hateoas\Factory;

use Hateoas\Configuration\RelationsRepository;
use Hateoas\Model\Link;

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

    public function __construct(RelationsRepository $relationsRepository, LinkFactory $linkFactory)
    {
        $this->relationsRepository = $relationsRepository;
        $this->linkFactory = $linkFactory;
    }

    /**
     * @param $object
     * @return Link[]
     */
    public function createLinks($object)
    {
        $relations = $this->relationsRepository->getRelations($object);

        $links = array();
        foreach ($relations as $relation) {
            $links[] = $this->linkFactory->createLink($object, $relation);
        }

        return $links;
    }
}
