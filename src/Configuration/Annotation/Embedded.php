<?php

declare(strict_types=1);

namespace Hateoas\Configuration\Annotation;

use JMS\Serializer\Annotation\AnnotationUtilsTrait;
use Symfony\Contracts\Service\Attribute\Required;

/**
 * @Annotation
 * @Target("ANNOTATION")
 */
#[\Attribute(0)]
class Embedded
{
    use AnnotationUtilsTrait;

    /**
     * @Required
     * @var mixed
     */
    #[Required]
    public $content;

    /**
     * @var string
     */
    public $type;

    /**
     * @var string
     */
    public $xmlElementName;

    /**
     * phpcs:disable
     * @var \Hateoas\Configuration\Annotation\Exclusion
     * phpcs:enable
     */
    public $exclusion = null;

    /**
     * @param string|array $content
     * @param Exclusion|null $exclusion
     */
    public function __construct(array $values = [], $content = null, ?string $type = null, ?string $xmlElementName = null, $exclusion = null)
    {
        $this->loadAnnotationParameters(get_defined_vars());
    }
}
