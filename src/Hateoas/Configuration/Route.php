<?php

declare(strict_types=1);

namespace Hateoas\Configuration;

use JMS\Serializer\Expression\Expression;

class Route
{
    /**
     * @var string|Expression
     */
    private $name;

    /**
     * @var Expression|string|array|string[]|Expression[]
     */
    private $parameters;

    /**
     * @var bool|Expression
     */
    private $absolute;

    /**
     * @var string|null
     */
    private $generator;

    /**
     * @param string|Expression $name
     * @param string|array $parameters
     * @param bool|Expression $absolute
     * @param string|null $generator
     */
    public function __construct($name, $parameters = [], $absolute = false, $generator = null)
    {
        $this->name = $name;
        $this->parameters = $parameters;
        $this->absolute = $absolute;
        $this->generator = $generator;
    }

    /**
     * @return Expression|string|array|string[]|Expression[]
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string|array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @return bool|Expression
     */
    public function isAbsolute()
    {
        return $this->absolute;
    }

    public function getGenerator(): ?string
    {
        return $this->generator;
    }
}
