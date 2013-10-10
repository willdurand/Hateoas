<?php

namespace tests\fixtures;

use Hateoas\Configuration\Annotation as Hateoas;

/**
 * @Hateoas\Relation("self", href = "http://adrienbrault.fr")
 * @Hateoas\Relation("computer", href = "http://www.apple.com/macbook-pro/", embed = "expr(object.getMacbookPro())")
 * @Hateoas\Relation("broken-computer", embed = "expr(object.getWindowsComputer())")
 */
class AdrienBrault
{
    private $firstName = 'Adrien';
    private $lastName = 'Brault';

    public function getMacbookPro()
    {
        return new Computer('MacBook Pro');
    }

    public function getWindowsComputer()
    {
        return new Computer('Windows Computer');
    }
}
