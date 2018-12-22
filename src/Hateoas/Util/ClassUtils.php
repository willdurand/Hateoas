<?php

namespace Hateoas\Util;

use Doctrine\Common\Persistence\Proxy;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class ClassUtils
{
    public static function getClass(object $object): string
    {
        $class = get_class($object);

        if (!interface_exists(Proxy::class, false)) {
            return $class;
        }

        if (false === $pos = strrpos($class, '\\' . Proxy::MARKER . '\\')) {
            return $class;
        }

        return substr($class, $pos + Proxy::MARKER_LENGTH + 2);
    }
}
