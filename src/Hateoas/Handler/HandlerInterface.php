<?php

namespace Hateoas\Handler;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
interface HandlerInterface
{
    public function transform($value, $data);
}
