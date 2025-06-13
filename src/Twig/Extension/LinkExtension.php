<?php

declare(strict_types=1);

namespace Hateoas\Twig\Extension;

use Hateoas\Helper\LinkHelper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class LinkExtension extends AbstractExtension
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
    public function getFunctions(): array
    {
        return [
            new TwigFunction('link_href', [$this->linkHelper, 'getLinkHref']),
        ];
    }

    public function getName(): string
    {
        return 'hateoas_link';
    }
}
