<?php

namespace Hateoas\Tests\Fixtures;

use JMS\Serializer\Annotation as Serializer;

/**
 * Demonstrates how to override the xml root name of a PaginatedCollection
 *
 * @Serializer\XmlRoot("users")
 */
class UsersRepresentation
{
    /**
     * @Serializer\Inline
     */
    private $inline;

    public function __construct($inline)
    {
        $this->inline = $inline;
    }
}
