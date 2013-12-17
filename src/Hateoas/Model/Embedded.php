<?php

namespace Hateoas\Model;

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

    /**
     * @param string      $rel
     * @param mixed       $data
     * @param string|null $xmlElementName
     */
    public function __construct($rel, $data, $xmlElementName = null)
    {
        $this->rel            = $rel;
        $this->data           = $data;
        $this->xmlElementName = $xmlElementName;
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
}
