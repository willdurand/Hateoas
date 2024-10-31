<?php

declare(strict_types=1);

namespace Hateoas\Representation;

use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\ExclusionPolicy("all")
 * @Serializer\XmlRoot("collection")
 *
 * @Hateoas\Relation(
 *     "items",
 *     embedded = @Hateoas\Embedded("expr(object.getResources())")
 * )
 */
#[Serializer\ExclusionPolicy('all')]
#[Serializer\XmlRoot('collection')]
#[Hateoas\Relation(
    'items',
    embedded: new Hateoas\Embedded(
        content: 'expr(object.getResources())',
    ),
)]
class CollectionRepresentation
{
    /**
     * @var mixed
     */
    private $resources;

    /**
     * @param array|\Traversable $resources
     */
    public function __construct($resources)
    {
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
