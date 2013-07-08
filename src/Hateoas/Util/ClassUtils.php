<?php

namespace Hateoas\Util;

use Doctrine\Common\Util\ClassUtils as DoctrineClassUtils;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class ClassUtils
{
    public static function getClass($object)
    {
        return self::getRealClass(get_class($object));
    }

    public static function getRealClass($class)
    {
        if (class_exists('Doctrine\Common\Util\ClassUtils')) {
            $class = DoctrineClassUtils::getRealClass($class);
        }

        return $class;
    }
}
