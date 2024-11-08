<?php

declare(strict_types=1);

namespace Hateoas\Configuration\Annotation;

use JMS\Serializer\Annotation\AnnotationUtilsTrait;

/**
 * @Annotation
 * @Target("ANNOTATION")
 */
#[\Attribute]
class Route
{
    use AnnotationUtilsTrait;

    /**
     * @Required
     * @var string
     */
    public $name;

    /**
     * @Required
     * @var mixed
     */
    public $parameters = [];

    /**
     * @var mixed
     */
    public $absolute = false;

    /**
     * @var string
     */
    public $generator = null;

    /**
     * @param array|string|null $values
     * @param array|string $parameters
     * @param bool|string $absolute
     */
    public function __construct($values = [], ?string $name = null, $parameters = [], $absolute = false, ?string $generator = null)
    {
        $this->loadAnnotationParameters(get_defined_vars());
    }
}
