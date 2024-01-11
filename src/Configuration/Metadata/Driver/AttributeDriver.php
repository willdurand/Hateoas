<?php

namespace Hateoas\Configuration\Metadata\Driver;

class AttributeDriver extends AnnotationOrAttributeDriver
{
    /**
     * {@inheritdoc}
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