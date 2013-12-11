<?php

namespace Hateoas\Representation;

use Hateoas\Configuration\Annotation as Hateoas;
use Hateoas\Configuration\Embed;
use Hateoas\Configuration\Exclusion;
use Hateoas\Configuration\Metadata\ClassMetadataInterface;
use Hateoas\Configuration\Relation;
use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\ExclusionPolicy("all")
 * @Serializer\XmlRoot("collection")
 *
 * @Hateoas\RelationProvider("getRelations")
 *
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class SimpleCollection
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

    /**
     * @var null|Exclusion
     */
    private $exclusion;

    /**
     * @var null|Exclusion
     */
    private $embedExclusion;

    /**
     * @var array
     */
    private $relations;

    public function __construct($resources, $rel = null, $xmlElementName = null, Exclusion $exclusion = null, Exclusion $embedExclusion = null, array $relations = null)
    {
        $this->resources = $resources;
        $this->rel = $rel ?: 'items';
        $this->xmlElementName = $xmlElementName;
        $this->exclusion = $exclusion;
        $this->embedExclusion = $embedExclusion;
        $this->relations = $relations;
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

    public function getRelations($object, ClassMetadataInterface $classMetadata)
    {
        if (null !== $this->relations) {
            return $this->relations;
        }

        return array(
            new Relation(
                $this->rel,
                null,
                new Embed(
                    'expr(object.getResources())',
                    $this->xmlElementName,
                    $this->embedExclusion
                ),
                array(),
                $this->exclusion
            )
        );
    }
}
