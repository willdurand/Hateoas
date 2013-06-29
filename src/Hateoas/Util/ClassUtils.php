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
        $class = get_class($object);

        if (class_exists('Doctrine\Common\Util\ClassUtils')) {
            $class = DoctrineClassUtils::getRealClass($class);
        }

        return $class;
    }
}
