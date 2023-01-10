<?php

declare(strict_types=1);

namespace Hateoas\Configuration\Annotation;

use JMS\Serializer\Annotation\AnnotationUtilsTrait;
use Symfony\Contracts\Service\Attribute\Required;

/**
 * @Annotation
 * @Target("CLASS")
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
final class Relation
{
    use AnnotationUtilsTrait;

    /**
     * @Required
     * @var string
     */
    #[Required]
    public $name;

    /**
     * @var mixed
     */
    public $href = null;

    /**
     * @var mixed
     */
    public $embedded = null;

    /**
     * @var array
     */
    public $attributes = [];

    /**
     * phpcs:disable
     * @var \Hateoas\Configuration\Annotation\Exclusion
     * phpcs:enable
     */
    public $exclusion = null;

    public function __construct($values = [], ?string $name = null, $href = null, $embedded = null, array $attributes = [], ?Exclusion $exclusion = null)
    {
        $this->loadAnnotationParameters(get_defined_vars());
    }
}
