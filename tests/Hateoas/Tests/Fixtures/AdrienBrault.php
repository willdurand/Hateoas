<?php

declare(strict_types=1);

namespace Hateoas\Tests\Fixtures;

use Hateoas\Configuration\Annotation as Hateoas;
use Hateoas\Configuration\Relation;
use JMS\Serializer\Annotation as Serializer;

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
 *          type="Hateoas\Tests\Fixtures\Computer",
 *          exclusion = @Hateoas\Exclusion(groups = {"Default"})
 *      )
 * )
 * @Hateoas\Relation(
 *      "broken-computer",
 *
 *      embedded = "expr(object.getWindowsComputer())"
 * )
 * @Hateoas\Relation(
 *      "smartphone",
 *      embedded = "expr(object.getiOSSmartphone())"
 * )
 * @Hateoas\Relation(
 *      "smartphone",
 *      embedded = "expr(object.getAndroidSmartphone())"
 * )
 * @Hateoas\RelationProvider("Hateoas\Tests\Fixtures\AdrienBrault::getRelations")
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

    public function getiOSSmartphone()
    {
        return new Smartphone('iPhone 6');
    }

    public function getAndroidSmartphone()
    {
        return new Smartphone('Nexus 5');
    }

    public static function getRelations()
    {
        return [
            new Relation('dynamic-relation', 'awesome!!!', ['wowowow']),
        ];
    }
}
