<?php

declare(strict_types=1);

namespace Hateoas\Representation;

use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\ExclusionPolicy("all")
 * @Serializer\XmlRoot("resource")
 *
 * @Hateoas\Relation(
 *      "help",
 *       href = "expr(object.getHelp())",
 *      exclusion = @Hateoas\Exclusion(
 *          excludeIf = "expr(object.getHelp() === null)"
 *      )
 * )
 * @Hateoas\Relation(
 *      "describes",
 *      href = "expr(object.getDescribes())",
 *      exclusion = @Hateoas\Exclusion(
 *          excludeIf = "expr(object.getDescribes() === null)"
 *      )
 * )
 * @Hateoas\Relation(
 *      "about",
 *      href = "expr(object.getAbout())",
 *      exclusion = @Hateoas\Exclusion(
 *          excludeIf = "expr(object.getAbout() === null)"
 *      )
 * )
 */
#[Serializer\ExclusionPolicy('all')]
#[Serializer\XmlRoot('resource')]
#[Hateoas\Relation(
    'help',
    href: 'expr(object.getHelp())',
    exclusion: new Hateoas\Exclusion(
        excludeIf: 'expr(object.getHelp() === null)',
    ),
)]
#[Hateoas\Relation(
    'describes',
    href: 'expr(object.getDescribes())',
    exclusion: new Hateoas\Exclusion(
        excludeIf: 'expr(object.getDescribes() === null)',
    ),
)]
#[Hateoas\Relation(
    'about',
    href: 'expr(object.getAbout())',
    exclusion: new Hateoas\Exclusion(
        excludeIf: 'expr(object.getAbout() === null)',
    ),
)]
class VndErrorRepresentation
{
    /**
     * @Serializer\Expose
     * @Serializer\Type("string")
     *
     * @var string
     */
    #[Serializer\Expose]
    #[Serializer\Type('string')]
    private $message;

    /**
     * @Serializer\Expose
     * @Serializer\XmlAttribute
     * @Serializer\Type("int")
     *
     * @var int
     */
    #[Serializer\Expose]
    #[Serializer\XmlAttribute]
    #[Serializer\Type('integer')]
    private $logref;

    /**
     * @var string
     */
    private $about;

    /**
     * @var string
     */
    private $help;

    /**
     * @var string
     */
    private $describes;

    public function __construct(string $message, ?int $logref = null, ?string $help = null, ?string $describes = null, ?string $about = null)
    {
        $this->message = $message;
        $this->logref = $logref;
        $this->help = $help;
        $this->describes = $describes;
        $this->about = $about;
    }

    public function getHelp(): ?string
    {
        return $this->help;
    }

    public function getDescribes(): ?string
    {
        return $this->describes;
    }

    public function getAbout(): ?string
    {
        return $this->about;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getLogref(): ?int
    {
        return $this->logref;
    }
}
