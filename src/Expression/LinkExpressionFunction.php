<?php

declare(strict_types=1);

namespace Hateoas\Expression;

use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

class LinkExpressionFunction implements ExpressionFunctionProviderInterface
{
    /**
     * @return ExpressionFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new ExpressionFunction('link', static function ($object, $rel, $absolute = false) {
                return sprintf('$link_helper->getLinkHref(%s, %s, %s)', $object, $rel, $absolute);
            }, static function ($context, $object, $rel, $absolute = false) {
                return $context['link_helper']->getLinkHref($object, $rel, $absolute);
            }),
        ];
    }
}
