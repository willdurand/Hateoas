<?php

namespace Hateoas\Twig\Extension;

use Hateoas\Helper\LinkHelper;

/**
 * @author William Durand <william.durand1@gmail.com>
 */
class LinkExtension extends \Twig_Extension
{
    /**
     * @var LinkHelper
     */
    private $linkHelper;

    public function __construct(LinkHelper $linkHelper)
    {
        $this->linkHelper = $linkHelper;
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('link_href', array($this->linkHelper, 'getLinkHref')),
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'hateoas_link';
    }
}
