<?php

namespace Hateoas\Handler;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class PropertyPathHandler implements HandlerInterface
{
    /**
     * @var Parser\PropertyPathParser
     */
    private $parser;

    /**
     * @var PropertyAccessor
     */
    private $propertyAccessor;

    public function __construct(Parser\PropertyPathParser $parser = null, PropertyAccessor $propertyAccessor = null)
    {
        $this->parser = $parser ?: new Parser\PropertyPathParser();
        $this->propertyAccessor = $propertyAccessor ?: PropertyAccess::createPropertyAccessor();
    }

    /**
     * {@inheritdoc}
     */
    public function transform($value, $data)
    {
        $propertyPath = $this->parser->getPropertyPath($value);

        return $this->propertyAccessor->getValue($data, $propertyPath);
    }
}
