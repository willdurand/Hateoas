<?php

namespace Hateoas\Configuration\Annotation;

/**
 * @Annotation
 * @Target("ANNOTATION")
 *
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class Embedded
{
    /**
     * @Required
     */
    public $content;

    /**
     * @var string
     */
    public $xmlElementName;

    /**
     * @var \Hateoas\Configuration\Annotation\Exclusion
     */
    public $exclusion = null;
}
