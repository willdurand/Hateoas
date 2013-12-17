<?php

namespace Hateoas\Helper;

use Hateoas\Configuration\RelationsRepository;
use Hateoas\Configuration\Relation;
use Hateoas\Configuration\Route;
use Hateoas\Factory\LinkFactory;

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
     * @var RelationsRepository
     */
    private $relationsRepository;

    /**
     * @param LinkFactory         $linkFactory
     * @param RelationsRepository $relationsRepository
     */
    public function __construct(LinkFactory $linkFactory, RelationsRepository $relationsRepository)
    {
        $this->linkFactory         = $linkFactory;
        $this->relationsRepository = $relationsRepository;
    }

    /**
     * @param object  $object
     * @param string  $rel
     * @param boolean $absolute
     *
     * @return string
     */
    public function getLinkHref($object, $rel, $absolute = false)
    {
        foreach ($this->relationsRepository->getRelations($object) as $relation) {
            if ($rel === $relation->getName()) {
                $relation = $this->patchAbsolute($relation, $absolute);

                if (null !== $link = $this->linkFactory->createLink($object, $relation)) {
                    return $link->getHref();
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
