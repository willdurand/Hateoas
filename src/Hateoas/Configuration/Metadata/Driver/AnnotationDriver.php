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

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class AnnotationDriver implements DriverInterface
{
    /**
     * @var AnnotationsReader
     */
    private $reader;

    /**
     * @var RelationProviderInterface
     */
    private $relationProvider;


    public function __construct(AnnotationsReader $reader, RelationProviderInterface $relationProvider)
    {
        $this->reader = $reader;
        $this->relationProvider = $relationProvider;
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
                    $annotation->attributes ?: array(),
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
            $exclusion->sinceVersion,
            $exclusion->untilVersion,
            $exclusion->maxDepth,
            $exclusion->excludeIf
        );
    }

    private function createHref($href)
    {
        if ($href instanceof Annotation\Route) {
            $href = new Route($href->name, $href->parameters, $href->absolute, $href->generator);
        }

        return $href;
    }

    private function createEmbedded($embedded)
    {
        if ($embedded instanceof Annotation\Embedded) {
            $embeddedExclusion = $embedded->exclusion;

            if (null !== $embeddedExclusion) {
                $embeddedExclusion = $this->parseExclusion($embeddedExclusion);
            }

            $embedded = new Embedded($embedded->content, $embedded->xmlElementName, $embeddedExclusion);
        }

        return $embedded;
    }

    private function createExclusion($exclusion)
    {
        if (null !== $exclusion) {
            $exclusion = $this->parseExclusion($exclusion);
        }

        return $exclusion;
    }
}
