<?php

namespace Hateoas\Handler\Parser;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class PropertyPathParser
{
    /**
     * @param $value string A string like "@this.author" where author is the property path
     * @return string
     */
    public function getPropertyPath($value)
    {
        return substr($value, strlen('@this.'));
    }
}
