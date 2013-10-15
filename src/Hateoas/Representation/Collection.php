<?php

namespace Hateoas\Representation;

use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\ExclusionPolicy("all")
 * @Serializer\XmlRoot("collection")
 *
 * @Hateoas\Relation(
 *      "expr(object.getRel())",
 *      embed = @Hateoas\Embed(
 *          "expr(object.getResources())",
 *          xmlElementName = "expr(object.getXmlElementName())"
 *      )
 * )
 *
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class Collection
{
    /**
     * @var string
     */
    private $rel;

    /**
     * @var mixed
     */
    private $resources;

    /**
     * @var null|string
     */
    private $xmlElementName;

    public function __construct($resources, $rel = null, $xmlElementName = null)
    {
        $this->resources = $resources;
        $this->rel = $rel ?: 'items';
        $this->xmlElementName = $xmlElementName;
    }

    /**
     * @return string
     */
    public function getRel()
    {
        return $this->rel;
    }

    /**
     * @param string $rel
     */
    public function setRel($rel)
    {
        $this->rel = $rel;
    }

    /**
     * @return mixed
     */
    public function getResources()
    {
        return $this->resources;
    }

    /**
     * @return null|string
     */
    public function getXmlElementName()
    {
        return $this->xmlElementName;
    }

    /**
     * @param null|string $xmlElementName
     */
    public function setXmlElementName($xmlElementName)
    {
        $this->xmlElementName = $xmlElementName;
    }
}
