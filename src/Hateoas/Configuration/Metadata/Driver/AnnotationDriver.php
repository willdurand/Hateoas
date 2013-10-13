<?php

namespace Hateoas\Configuration\Metadata\Driver;

use Doctrine\Common\Annotations\Reader as AnnotationsReader;
use Hateoas\Configuration\Annotation;
use Hateoas\Configuration\Embed;
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
                $href = $annotation->href;

                if ($href instanceof Annotation\Route) {
                    $href = new Route($href->name, $href->parameters, $href->absolute, $href->generator);
                }

                $embed = $annotation->embed;

                if ($embed instanceof Annotation\Embed) {
                    $embedExclusion = $embed->exclusion;
                    if (null !== $embedExclusion) {
                        $embedExclusion = $this->parseExclusion($embedExclusion);
                    }

                    $embed = new Embed($embed->content, $embed->xmlElementName, $embedExclusion);
                }

                $exclusion = $annotation->exclusion;
                if (null !== $exclusion) {
                    $exclusion = $this->parseExclusion($exclusion);
                }

                $classMetadata->addRelation(new Relation(
                    $annotation->name,
                    $href,
                    $embed,
                    $annotation->attributes ?: array(),
                    $exclusion
                ));
            } elseif ($annotation instanceof Annotation\RelationProvider) {
                $classMetadata->addRelationProvider(new RelationProvider($annotation->name));
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
}
