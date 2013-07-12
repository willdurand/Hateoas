<?php

namespace Hateoas\Model;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class Embed
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
    private $xmlRootName;

    public function __construct($rel, $data, $xmlRootName = null)
    {
        $this->rel = $rel;
        $this->data = $data;
        $this->xmlRootName = $xmlRootName;
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
    public function getXmlRootName()
    {
        return $this->xmlRootName;
    }
}
