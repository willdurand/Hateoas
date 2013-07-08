<?php

namespace Hateoas\Handler;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
interface HandlerInterface
{
    /**
     * @param  string $value
     * @param  mixed  $data
     * @return mixed
     */
    public function transform($value, $data);
}
