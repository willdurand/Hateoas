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
     * @var PropertyAccessor
     */
    private $propertyAccessor;

    public function __construct(PropertyAccessor $propertyAccessor = null)
    {
        $this->propertyAccessor = $propertyAccessor ?: PropertyAccess::createPropertyAccessor();
    }

    /**
     * {@inheritdoc}
     */
    public function transform($propertyPath, $data)
    {
        return $this->propertyAccessor->getValue($data, $propertyPath);
    }
}
