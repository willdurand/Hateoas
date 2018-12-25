<?php

declare(strict_types=1);

namespace Hateoas\Model;

use Hateoas\Serializer\Metadata\RelationPropertyMetadata;

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
     * @var array
     */
    private $type;

    /**
     * @param mixed $data
     * @param string|null $xmlElementName
     */
    public function __construct(string $rel, $data, RelationPropertyMetadata $metadata, $xmlElementName = null, ?array $type = null)
    {
        $this->rel            = $rel;
        $this->data           = $data;
        $this->metadata       = $metadata;
        $this->xmlElementName = $xmlElementName;
        $this->type = $type;
    }

    public function getType(): ?array
    {
        return $this->type;
    }

    public function getMetadata(): RelationPropertyMetadata
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

    public function getRel(): string
    {
        return $this->rel;
    }

    public function getXmlElementName(): ?string
    {
        return $this->xmlElementName;
    }
}
