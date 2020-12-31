<?php

declare(strict_types=1);

namespace Hateoas\Configuration\Metadata\Driver;

use Doctrine\Common\Annotations\Reader as AnnotationsReader;
use Hateoas\Configuration\Annotation;
use Hateoas\Configuration\Embedded;
use Hateoas\Configuration\Exclusion;
use Hateoas\Configuration\Metadata\ClassMetadata;
use Hateoas\Configuration\Provider\RelationProviderInterface;
use Hateoas\Configuration\Relation;
use Hateoas\Configuration\RelationProvider;
use Hateoas\Configuration\Route;
use JMS\Serializer\Expression\CompilableExpressionEvaluatorInterface;
use JMS\Serializer\Expression\Expression;
use JMS\Serializer\Type\ParserInterface;
use Metadata\ClassMetadata as JMSClassMetadata;
use Metadata\Driver\DriverInterface;

class AnnotationDriver implements DriverInterface
{
    use CheckExpressionTrait;

    /**
     * @var AnnotationsReader
     */
    private $reader;

    /**
     * @var RelationProviderInterface
     */
    private $relationProvider;

    /**
     * @var ParserInterface
     */
    private $typeParser;

    public function __construct(
        AnnotationsReader $reader,
        CompilableExpressionEvaluatorInterface $expressionLanguage,
        RelationProviderInterface $relationProvider,
        ParserInterface $typeParser
    ) {
        $this->reader = $reader;
        $this->relationProvider = $relationProvider;
        $this->expressionLanguage = $expressionLanguage;
        $this->typeParser = $typeParser;
    }

    public function loadMetadataForClass(\ReflectionClass $class): ?JMSClassMetadata
    {
        $annotations = $this->reader->getClassAnnotations($class);

        if (0 === count($annotations)) {
            return null;
        }

        $classMetadata = new ClassMetadata($class->getName());
        $classMetadata->fileResources[] = $class->getFilename();

        foreach ($annotations as $annotation) {
            if ($annotation instanceof Annotation\Relation) {
                $classMetadata->addRelation(new Relation(
                    $annotation->name,
                    $this->createHref($annotation->href),
                    $this->createEmbedded($annotation->embedded),
                    $this->checkExpressionArray($annotation->attributes) ?: [],
                    $this->createExclusion($annotation->exclusion)
                ));
            } elseif ($annotation instanceof Annotation\RelationProvider) {
                $relations = $this->relationProvider->getRelations(new RelationProvider($annotation->name), $class->getName());
                foreach ($relations as $relation) {
                    $classMetadata->addRelation($relation);
                }
            }
        }

        if (0 === count($classMetadata->getRelations())) {
            return null;
        }

        return $classMetadata;
    }

    private function parseExclusion(Annotation\Exclusion $exclusion): Exclusion
    {
        return new Exclusion(
            $exclusion->groups,
            null !== $exclusion->sinceVersion ? (string) $exclusion->sinceVersion : null,
            null !== $exclusion->untilVersion ? (string) $exclusion->untilVersion : null,
            null !== $exclusion->maxDepth ? (int) $exclusion->maxDepth : null,
            $this->checkExpression($exclusion->excludeIf)
        );
    }

    /**
     * @param mixed $href
     *
     * @return Expression|mixed
     */
    private function createHref($href)
    {
        if ($href instanceof Annotation\Route) {
            return new Route(
                $this->checkExpression($href->name),
                is_array($href->parameters) ? $this->checkExpressionArray($href->parameters) : $this->checkExpression($href->parameters),
                $this->checkExpression($href->absolute),
                $href->generator
            );
        }

        return $this->checkExpression($href);
    }

    /**
     * @param Annotation\Embedded|mixed $embedded
     *
     * @return Expression|mixed
     */
    private function createEmbedded($embedded)
    {
        if ($embedded instanceof Annotation\Embedded) {
            $embeddedExclusion = $embedded->exclusion;

            if (null !== $embeddedExclusion) {
                $embeddedExclusion = $this->parseExclusion($embeddedExclusion);
            }

            return new Embedded(
                $this->checkExpression($embedded->content),
                $this->checkExpression($embedded->xmlElementName),
                $embeddedExclusion,
                null !== $embedded->type ? $this->typeParser->parse($embedded->type) : null
            );
        }

        return $this->checkExpression($embedded);
    }

    private function createExclusion(?Annotation\Exclusion $exclusion = null): ?Exclusion
    {
        if (null !== $exclusion) {
            $exclusion = $this->parseExclusion($exclusion);
        }

        return $exclusion;
    }
}
