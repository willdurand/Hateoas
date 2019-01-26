<?php

declare(strict_types=1);

namespace Hateoas\UrlGenerator;

interface UrlGeneratorInterface
{
    /**
     * @param bool|int $absolute
     */
    public function generate(string $name, array $parameters, $absolute = false): string;
}
