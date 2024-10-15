<?php

declare(strict_types=1);

namespace Hateoas\Configuration\Metadata\Driver;

use Doctrine\Common\Annotations\Reader as AnnotationsReader;
use Hateoas\Configuration\Provider\RelationProviderInterface;
use JMS\Serializer\Expression\CompilableExpressionEvaluatorInterface;
use JMS\Serializer\Type\ParserInterface;

class AnnotationDriver extends AnnotationOrAttributeDriver
{
    /**
     * @var AnnotationsReader
     */
    private $reader;

    public function __construct(
        AnnotationsReader $reader,
        CompilableExpressionEvaluatorInterface $expressionLanguage,
        RelationProviderInterface $relationProvider,
        ParserInterface $typeParser
    ) {
        parent::__construct($expressionLanguage, $relationProvider, $typeParser);

        $this->reader = $reader;
    }

    /**
     * {@inheritDoc}
     */
    protected function getClassAnnotations(\ReflectionClass $class): array
    {
        return $this->reader->getClassAnnotations($class);
    }
}
