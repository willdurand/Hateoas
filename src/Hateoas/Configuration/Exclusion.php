<?php

declare(strict_types=1);

namespace Hateoas\Configuration;

use JMS\Serializer\Expression\ExpressionEvaluator;

class Exclusion
{
    /**
     * @var array|null
     */
    private $groups;

    /**
     * @var string|null
     */
    private $sinceVersion;

    /**
     * @var string|null
     */
    private $untilVersion;

    /**
     * @var int|null
     */
    private $maxDepth;

    /**
     * @var string|ExpressionEvaluator|null
     */
    private $excludeIf;

    /**
     * @param mixed $excludeIf
     */
    public function __construct(
        ?array $groups = null,
        ?string $sinceVersion = null,
        ?string $untilVersion = null,
        ?int $maxDepth = null,
        $excludeIf = null
    ) {
        $this->groups = $groups;
        $this->sinceVersion = $sinceVersion ?? null;
        $this->untilVersion = $untilVersion ?? null;
        $this->maxDepth = $maxDepth ?? null;
        $this->excludeIf = $excludeIf;
    }

    /**
     * @return array|null
     */
    public function getGroups(): ?array
    {
        return $this->groups;
    }

    public function getSinceVersion(): ?string
    {
        return $this->sinceVersion;
    }

    public function getUntilVersion(): ?string
    {
        return $this->untilVersion;
    }

    public function getMaxDepth(): ?int
    {
        return $this->maxDepth;
    }

    /**
     * @return ExpressionEvaluator|mixed|string|null
     */
    public function getExcludeIf()
    {
        return $this->excludeIf;
    }
}
