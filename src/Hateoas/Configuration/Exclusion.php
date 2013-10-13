<?php

namespace Hateoas\Configuration;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class Exclusion
{
    /**
     * @var null|array
     */
    private $groups;

    /**
     * @var null|float
     */
    private $sinceVersion;

    /**
     * @var null|float
     */
    private $untilVersion;

    /**
     * @var null|integer
     */
    private $maxDepth;

    /**
     * @var null|string
     */
    private $excludeIf;

    public function __construct(
        array $groups = null,
        $sinceVersion = null,
        $untilVersion = null,
        $maxDepth = null,
        $excludeIf = null
    ) {
        $this->groups = $groups;
        $this->sinceVersion = null !== $sinceVersion ? (float) $sinceVersion : null;
        $this->untilVersion = null !== $untilVersion ? (float) $untilVersion : null;
        $this->maxDepth = null !== $maxDepth ? (int) $maxDepth : null;
        $this->excludeIf = $excludeIf;
    }

    /**
     * @return null|array
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * @return null|float
     */
    public function getSinceVersion()
    {
        return $this->sinceVersion;
    }

    /**
     * @return null|float
     */
    public function getUntilVersion()
    {
        return $this->untilVersion;
    }

    /**
     * @return int|null
     */
    public function getMaxDepth()
    {
        return $this->maxDepth;
    }

    /**
     * @return null|string
     */
    public function getExcludeIf()
    {
        return $this->excludeIf;
    }
}
