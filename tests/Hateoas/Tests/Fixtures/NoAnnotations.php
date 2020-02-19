<?php

declare(strict_types=1);

namespace Hateoas\Tests\Fixtures;

class NoAnnotations
{
    private $id;
    private $number;
    private $unused = 'N/A';

    public function __construct(string $id, int $number)
    {
        $this->id = $id;
        $this->number = $number;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function number(): int
    {
        return $this->number;
    }
}
