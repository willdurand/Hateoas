<?php

namespace Hateoas\Representation;

use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation as Serializer;

/**
 * @Hateoas\Relation(
 *     "items",
 *     embedded = @Hateoas\Embedded("expr(object.getResources())")
 * )
 * @Serializer\ExclusionPolicy("all")
 * @Serializer\XmlRoot("collection")
 *
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class CollectionRepresentation
{

    /**
     * @var mixed
     */
    private $resources;

    /**
     * @param array|\Traversable $resources
     */
    public function __construct($resources) {
        if ($resources instanceof \Traversable) {
            $resources = iterator_to_array($resources);
        }

        $this->resources      = $resources;
    }

    /**
     * @return mixed
     */
    public function getResources()
    {
        return $this->resources;
    }
}
