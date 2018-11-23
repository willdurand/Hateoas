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
     * @var null|string
     */
    private $sinceVersion;

    /**
     * @var null|string
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
        $this->sinceVersion = null !== $sinceVersion ? $sinceVersion : null;
        $this->untilVersion = null !== $untilVersion ? $untilVersion : null;
        $this->maxDepth = null !== $maxDepth ? $maxDepth : null;
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
     * @return null|string
     */
    public function getSinceVersion()
    {
        return $this->sinceVersion;
    }

    /**
     * @return null|string
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
