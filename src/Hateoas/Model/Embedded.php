<?php

namespace Hateoas\Model;

use Hateoas\Serializer\Metadata\RelationPropertyMetadata;

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
     * @var RelationPropertyMetadata
     */
    private $metadata;

    /**
     * @param string $rel
     * @param mixed $data
     * @param string|null $xmlElementName
     * @param RelationPropertyMetadata $metadata
     */
    public function __construct($rel, $data, RelationPropertyMetadata $metadata, $xmlElementName = null)
    {
        $this->rel            = $rel;
        $this->data           = $data;
        $this->metadata       = $metadata;
        $this->xmlElementName = $xmlElementName;
    }

    /**
     * @return RelationPropertyMetadata
     */
    public function getMetadata()
    {
        return $this->metadata;
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
