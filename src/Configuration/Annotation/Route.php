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
class Route
{
    use AnnotationUtilsTrait;

    /**
     * @Required
     * @var string
     */
    #[Required]
    public $name;

    /**
     * @Required
     * @var mixed
     */
    #[Required]
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
     * @param array|string $parameters
     * @param bool|string $absolute
     */
    public function __construct(array $values = [], ?string $name = null, $parameters = null, $absolute = false, ?string $generator = null)
    {
        $this->loadAnnotationParameters(get_defined_vars());
    }
}
