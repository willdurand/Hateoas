<?php

declare(strict_types=1);

namespace Hateoas\Configuration;

use JMS\Serializer\Expression\Expression;

class Embedded
{
    /**
     * @var string|mixed
     */
    private $content;

    /**
     * @var string|Expression|null
     */
    private $xmlElementName;

    /**
     * @var Exclusion|null
     */
    private $exclusion;

    /**
     * @var array
     */
    private $type;

    /**
     * @param string|mixed $content
     * @param string|Expression|null  $xmlElementName
     */
    public function __construct($content, $xmlElementName = null, ?Exclusion $exclusion = null, ?array $type = null)
    {
        $this->content        = $content;
        $this->xmlElementName = $xmlElementName;
        $this->exclusion      = $exclusion;
        $this->type = $type;
    }

    public function getType(): ?array
    {
        return $this->type;
    }

    /**
     * @return mixed|string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @return Expression|string|null
     */
    public function getXmlElementName()
    {
        return $this->xmlElementName;
    }

    public function getExclusion(): ?Exclusion
    {
        return $this->exclusion;
    }
}
