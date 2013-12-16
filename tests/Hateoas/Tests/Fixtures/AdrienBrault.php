<?php

namespace Hateoas\Tests\Fixtures;

use Hateoas\Configuration\Relation;
use JMS\Serializer\Annotation as Serializer;
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * @Hateoas\Relation(
 *      "self",
 *      href = "http://adrienbrault.fr",
 *      exclusion = @Hateoas\Exclusion(
 *          groups = {"Default", "simple"},
 *          excludeIf = "expr(object.firstName !== 'Adrien' || object.lastName !== 'Brault')"
 *      )
 * )
 * @Hateoas\Relation(
 *      "computer",
 *      href = "http://www.apple.com/macbook-pro/",
 *      exclusion = @Hateoas\Exclusion(groups = {"Default", "simple"}),
 *      embedded = @Hateoas\Embedded(
 *          "expr(object.getMacbookPro())",
 *          exclusion = @Hateoas\Exclusion(groups = {"Default"})
 *      )
 * )
 * @Hateoas\Relation(
 *      "broken-computer",
 *      embedded = "expr(object.getWindowsComputer())"
 * )
 *
 * @Hateoas\RelationProvider("getRelations")
 */
class AdrienBrault
{
    /**
     * @Serializer\Groups({"Default", "simple"})
     */
    public $firstName = 'Adrien';

    /**
     * @Serializer\Groups({"Default", "simple"})
     */
    public $lastName = 'Brault';

    public function getMacbookPro()
    {
        return new Computer('MacBook Pro');
    }

    public function getWindowsComputer()
    {
        return new Computer('Windows Computer');
    }

    public function getRelations()
    {
        return array(
            new Relation('dynamic-relation', 'awesome!!!'),
        );
    }
}
