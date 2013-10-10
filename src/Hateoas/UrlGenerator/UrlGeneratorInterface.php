<?php

namespace Hateoas\UrlGenerator;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
interface UrlGeneratorInterface
{
    /**
     * @param string  $name
     * @param array   $parameters
     * @param boolean $absolute
     *
     * @return string
     */
    public function generate($name, array $parameters, $absolute = false);
}
