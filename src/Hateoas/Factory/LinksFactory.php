<?php

namespace Hateoas\Factory;

use Hateoas\Configuration\RelationsManagerInterface;
use Hateoas\Model\Link;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class LinksFactory
{
    /**
     * @var RelationsManagerInterface
     */
    private $relationsManager;

    /**
     * @var LinkFactory
     */
    private $linkFactory;

    public function __construct(RelationsManagerInterface $relationsManager, LinkFactory $linkFactory)
    {
        $this->relationsManager = $relationsManager;
        $this->linkFactory = $linkFactory;
    }

    /**
     * @param $object
     * @return Link[]
     */
    public function createLinks($object)
    {
        $relations = $this->relationsManager->getRelations($object);

        $links = array();
        foreach ($relations as $relation) {
            $links[] = $this->linkFactory->createLink($object, $relation);
        }

        return $links;
    }
}
