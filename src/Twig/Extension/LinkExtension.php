<?php

declare(strict_types=1);

namespace Hateoas\Twig\Extension;

use Hateoas\Helper\LinkHelper;

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
        return [
            new \Twig_SimpleFunction('link_href', [$this->linkHelper, 'getLinkHref']),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'hateoas_link';
    }
}
