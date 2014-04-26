<?php

require __DIR__ . '/vendor/autoload.php';

\Doctrine\Common\Annotations\AnnotationRegistry::registerLoader('class_exists');

use Hateoas\HateoasBuilder;
use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation as Serializer;
use JMS\Serializer\SerializationContext;

/**
 * @Serializer\ExclusionPolicy("all")
 * @Hateoas\Relation(
 *      "friend",
 *      embedded = @Hateoas\Embedded(
 *          "expr(object.getFriend())",
 *          exclusion = @Hateoas\Exclusion(maxDepth = 1)
 *      )
 * )
 */
class User
{
    /**
     * @Serializer\Expose
     */
    private $username;

    private $friend;

    function __construct($username)
    {
        $this->username = $username;
    }

    public function getFriend()
    {
        return $this->friend;
    }

    public function setFriend($friend)
    {
        $this->friend = $friend;
    }

}

$foo    = new User('foo');
$bar    = new User('bar');
$foobar = new User('foobar');
$baz    = new User('baz');

$foo->setFriend($bar);
$bar->setFriend($foobar);
$foobar->setFriend($baz);

$hateoas = HateoasBuilder::create()->build();

echo $hateoas->serialize($foo, 'json', SerializationContext::create()->enableMaxDepthChecks());
echo str_repeat(PHP_EOL, 2);
echo $hateoas->serialize($foo, 'xml', SerializationContext::create()->enableMaxDepthChecks());
