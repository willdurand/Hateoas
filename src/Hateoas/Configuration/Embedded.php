<?php

namespace Hateoas\Configuration;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class Embedded
{
    /**
     * @var string|mixed
     */
    private $content;

    /**
     * @var string|null
     */
    private $xmlElementName;

    /**
     * @var Exclusion|null
     */
    private $exclusion;

    /**
     * @param string|mixed $content
     * @param string|null  $xmlElementName
     * @param Exclusion    $exclusion
     */
    public function __construct($content, $xmlElementName = null, Exclusion $exclusion = null)
    {
        $this->content        = $content;
        $this->xmlElementName = $xmlElementName;
        $this->exclusion      = $exclusion;
    }

    /**
     * @return mixed|string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @return null|string
     */
    public function getXmlElementName()
    {
        return $this->xmlElementName;
    }

    /**
     * @return Exclusion|null
     */
    public function getExclusion()
    {
        return $this->exclusion;
    }
}
