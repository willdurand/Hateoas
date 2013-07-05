<?php

namespace tests\fixtures;

use Hateoas\Configuration\Annotation as Hateoas;

/**
 * @Hateoas\Relation("self", href = "http://adrienbrault.fr")
 * @Hateoas\Relation("computer", href = "http://www.apple.com/macbook-pro/", embed = "@this.macbookPro")
 */
class AdrienBrault
{
    private $firstName = 'Adrien';
    private $lastName = 'Brault';

    public function getMacbookPro()
    {
        return array(
            'name' => 'MacBook Pro',
        );
    }
}
