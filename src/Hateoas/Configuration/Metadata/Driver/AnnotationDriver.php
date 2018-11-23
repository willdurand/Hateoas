<?php

namespace Hateoas\Configuration\Metadata\Driver;

use Doctrine\Common\Annotations\Reader as AnnotationsReader;
use Hateoas\Configuration\Annotation;
use Hateoas\Configuration\Embedded;
use Hateoas\Configuration\Exclusion;
use Hateoas\Configuration\Metadata\ClassMetadata;
use Hateoas\Configuration\Provider\RelationProviderInterface;
use Hateoas\Configuration\RelationProvider;
use Metadata\ClassMetadata as JMSClassMetadata;
use Hateoas\Configuration\Relation;
use Hateoas\Configuration\Route;
use Metadata\Driver\DriverInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
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

    public function __construct(AnnotationsReader $reader, ExpressionLanguage $expressionLanguage, RelationProviderInterface $relationProvider)
    {
        $this->reader = $reader;
        $this->relationProvider = $relationProvider;
        $this->expressionLanguage = $expressionLanguage;
    }

    /**
     * {@inheritdoc}
     */
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
                    $this->checkExpressionArray($annotation->attributes) ?: array(),
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

    private function parseExclusion(Annotation\Exclusion $exclusion)
    {
        return new Exclusion(
            $exclusion->groups,
            $exclusion->sinceVersion !== null ? (string)$exclusion->sinceVersion : null,
            $exclusion->untilVersion !== null ? (string)$exclusion->untilVersion : null,
            $exclusion->maxDepth !== null ? (int)$exclusion->maxDepth : null,
            $this->checkExpression($exclusion->excludeIf)
        );
    }

    private function createHref($href)
    {
        if ($href instanceof Annotation\Route) {
            $href = new Route(
                $this->checkExpression($href->name),
                is_array($href->parameters) ? $this->checkExpressionArray($href->parameters) : $this->checkExpression($href->parameters),
                $this->checkExpression($href->absolute),
                $href->generator
            );
        }

        return $this->checkExpression($href);
    }

    private function createEmbedded($embedded)
    {
        if ($embedded instanceof Annotation\Embedded) {
            $embeddedExclusion = $embedded->exclusion;

            if (null !== $embeddedExclusion) {
                $embeddedExclusion = $this->parseExclusion($embeddedExclusion);
            }

            $embedded = new Embedded($this->checkExpression($embedded->content), $this->checkExpression($embedded->xmlElementName), $embeddedExclusion);
        }

        return $this->checkExpression($embedded);
    }

    private function createExclusion($exclusion)
    {
        if (null !== $exclusion) {
            $exclusion = $this->parseExclusion($exclusion);
        }

        return $exclusion;
    }
}
