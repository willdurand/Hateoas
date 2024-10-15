<?php

declare(strict_types=1);

namespace Hateoas\Configuration\Metadata\Driver;

class AttributeDriver extends AnnotationOrAttributeDriver
{
    /**
     * {@inheritDoc}
     */
    protected function getClassAnnotations(\ReflectionClass $class): array
    {
        return array_map(
            static function (\ReflectionAttribute $attribute): object {
                return $attribute->newInstance();
            },
            $class->getAttributes()
        );
    }
}
