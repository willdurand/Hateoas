<?php

namespace Hateoas\Configuration\Metadata\Driver;

use Doctrine\Common\Annotations\Reader as AnnotationsReader;
use Hateoas\Configuration\Annotation;
use Hateoas\Configuration\Embedded;
use Hateoas\Configuration\Exclusion;
use Hateoas\Configuration\Metadata\ClassMetadata;
use Hateoas\Configuration\Relation;
use Hateoas\Configuration\RelationProvider;
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
     * @param AnnotationsReader $reader
     */
    public function __construct(AnnotationsReader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * {@inheritdoc}
     */
    public function loadMetadataForClass(\ReflectionClass $class)
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
                $classMetadata->addRelationProvider(new RelationProvider($annotation->name));
            }
        }

        if (0 === count($classMetadata->getRelations()) && 0 === count($classMetadata->getRelationProviders())) {
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
