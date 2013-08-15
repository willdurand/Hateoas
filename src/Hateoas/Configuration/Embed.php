<?php

namespace Hateoas\Configuration;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class Embed
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
     * @param string|mixed $content
     * @param string\null  $xmlElementName
     */
    public function __construct($content, $xmlElementName = null)
    {
        $this->content        = $content;
        $this->xmlElementName = $xmlElementName;
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
}
