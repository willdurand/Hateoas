<?php

declare(strict_types=1);

namespace Hateoas\Configuration\Annotation;

use JMS\Serializer\Annotation\AnnotationUtilsTrait;

/**
 * @Annotation
 * @Target("ANNOTATION")
 */
#[\Attribute]
final class Exclusion
{
    use AnnotationUtilsTrait;

    /**
     * @var array
     */
    public $groups = null;

    /**
     * @var string
     */
    public $sinceVersion = null;

    /**
     * @var string
     */
    public $untilVersion = null;

    /**
     * If you are working with the FOS/Rest Bundle, be reminded to set
     * Rest\View(serializerEnableMaxDepthChecks=true)
     * on your controller
     *
     * @var int
     */
    public $maxDepth = null;

    /**
     * @var string
     */
    public $excludeIf = null;

    /**
     * @param array|null $values
     */
    public function __construct($values = [], ?array $groups = null, ?string $sinceVersion = null, ?string $untilVersion = null, ?int $maxDepth = null, ?string $excludeIf = null)
    {
        $this->loadAnnotationParameters(get_defined_vars());
    }
}
