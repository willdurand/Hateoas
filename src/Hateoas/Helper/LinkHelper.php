<?php

namespace Hateoas\Helper;

use Hateoas\Configuration\RelationsRepository;
use Hateoas\Configuration\Relation;
use Hateoas\Configuration\Route;
use Hateoas\Expression\ExpressionFunctionInterface;
use Hateoas\Factory\LinkFactory;

/**
 * @author William Durand <william.durand1@gmail.com>
 */
class LinkHelper implements ExpressionFunctionInterface
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

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'link';
    }

    /**
     * {@inheritDoc}
     */
    public function getCompiler()
    {
        return function ($object, $rel, $absolute = false) {
            return sprintf('$link_helper->getLinkHref(%s, $s, $s)', $object, $rel, $absolute);
        };
    }

    /**
     * {@inheritDoc}
     */
    public function getEvaluator()
    {
        return function ($context, $object, $rel, $absolute = false) {
            return $context['link_helper']->getLinkHref($object, $rel, $absolute);
        };
    }

    /**
     * {@inheritDoc}
     */
    public function getContextValues()
    {
        return array('link_helper' => $this);
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
            $relation->getEmbed(),
            $relation->getAttributes(),
            $relation->getExclusion()
        );
    }
}
