<?php

namespace Hateoas\Factory\Definition;

class EmbedDefinition
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $accessor;

    public function __construct($name, $accessor = null)
    {
        $this->name = $name;
        $this->accessor = $accessor;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getAccessor()
    {
        return 'get'.ucfirst($this->accessor ?: $this->getName());
    }
}
