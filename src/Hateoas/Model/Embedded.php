<?php

namespace Hateoas\Model;

use Hateoas\Configuration\Exclusion;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class Embedded
{
    /**
     * @var string
     */
    private $rel;

    /**
     * @var mixed
     */
    private $data;

    /**
     * @var string|null
     */
    private $xmlElementName;

    private $exclusion;

    /**
     * @param string      $rel
     * @param mixed       $data
     * @param string|null $xmlElementName
     */
    public function __construct($rel, $data, $xmlElementName = null, Exclusion $exclusion = null)
    {
        $this->rel            = $rel;
        $this->data           = $data;
        $this->xmlElementName = $xmlElementName;
        $this->exclusion = $exclusion;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function getRel()
    {
        return $this->rel;
    }

    /**
     * @return null|string
     */
    public function getXmlElementName()
    {
        return $this->xmlElementName;
    }

    /**
     * @return Exclusion
     */
    public function getExclusion()
    {
        return $this->exclusion;
    }
}
