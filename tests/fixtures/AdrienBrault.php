<?php

namespace tests\fixtures;

use Hateoas\Configuration\Annotation as Hateoas;

/**
 * @Hateoas\Relation("self", href = "http://adrienbrault.fr")
 * @Hateoas\Relation("computer", href = "http://www.apple.com/macbook-pro/", embed = "expr(object.getMacbookPro())")
 */
class AdrienBrault
{
    private $firstName = 'Adrien';
    private $lastName = 'Brault';

    public function getMacbookPro()
    {
        return new Computer('MacBook Pro');
    }
}
