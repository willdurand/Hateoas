<?php

namespace Hateoas\Expression;

use Hateoas\Helper\LinkHelper;

/**
 * @author William Durand <william.durand1@gmail.com>
 */
class LinkExpressionFunction implements ExpressionFunctionInterface
{
    /**
     * @var LinkHelper
     */
    private $linkHelper;

    /**
     * @param LinkHelper $linkHelper
     */
    public function __construct(LinkHelper $linkHelper)
    {
        $this->linkHelper = $linkHelper;
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
            return sprintf('$link_helper->getLinkHref(%s, %s, %s)', $object, $rel, $absolute);
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
    public function getContextVariables()
    {
        return array('link_helper' => $this->linkHelper);
    }
}
