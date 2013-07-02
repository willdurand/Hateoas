<?php

namespace tests\fixtures;

use Hateoas\Configuration\Annotation as Hateoas;

/**
 * @Hateoas\Relation("self", href = "http://adrienbrault.fr")
 */
class AdrienBrault
{
    private $firstName = 'Adrien';
    private $lastName = 'Brault';
}
