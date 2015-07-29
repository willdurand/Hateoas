<?php

namespace Hateoas\Representation;

use Hateoas\Configuration\Relation;
use Hateoas\Configuration\Annotation as Hateoas;
use Hateoas\Configuration\Metadata\ClassMetadataInterface;
use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\ExclusionPolicy("all")
 * @Serializer\XmlRoot("resource")
 *
 * @Hateoas\RelationProvider("getRelations")
 *
 * @author William Durand <william.durand1@gmail.com>
 */
class VndErrorRepresentation
{
    /**
     * @Serializer\Expose
     */
    private $message;

    /**
     * @Serializer\Expose
     * @Serializer\XmlAttribute
     */
    private $logref;

    /**
     * @var Relation
     */
    private $help;

    /**
     * @var Relation
     */
    private $describes;

    /**
     * @param string $message
     * @param integer $logref
     */
    public function __construct($message, $logref = null, Relation $help = null, Relation $describes = null)
    {
        $this->message   = $message;
        $this->logref    = $logref;
        $this->help      = $help;
        $this->describes = $describes;
    }

    public function getRelations($object, ClassMetadataInterface $classMetadata)
    {
        $relations = array();

        if (null !== $this->help) {
            $relations[] = $this->help;
        }

        if (null !== $this->describes) {
            $relations[] = $this->describes;
        }

        return $relations;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getLogref()
    {
        return $this->logref;
    }
}
