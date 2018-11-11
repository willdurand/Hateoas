<?php

namespace Hateoas\Expression;

use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

/**
 * @author William Durand <william.durand1@gmail.com>
 */
class LinkExpressionFunction implements ExpressionFunctionProviderInterface
{
    /**
     * @return ExpressionFunction[] An array of Function instances
     */
    public function getFunctions()
    {
        return [
            new ExpressionFunction('link', function ($object, $rel, $absolute = false) {
                return sprintf('$link_helper->getLinkHref(%s, %s, %s)', $object, $rel, $absolute);
            }, function ($context, $object, $rel, $absolute = false) {
                return $context['link_helper']->getLinkHref($object, $rel, $absolute);
            })
        ];
    }
}
