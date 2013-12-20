<?php

namespace Hateoas\Configuration\Metadata\Driver;

use Hateoas\Configuration\Embedded;
use Hateoas\Configuration\Exclusion;
use Hateoas\Configuration\Metadata\ClassMetadata;
use Hateoas\Configuration\Relation;
use Hateoas\Configuration\RelationProvider;
use Hateoas\Configuration\Route;
use Metadata\Driver\AbstractFileDriver;
use Symfony\Component\Yaml\Yaml;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class YamlDriver extends AbstractFileDriver
{
    /**
     * {@inheritdoc}
     */
    protected function loadMetadataFromFile(\ReflectionClass $class, $file)
    {
        $config = Yaml::parse(file_get_contents($file));

        if (!isset($config[$name = $class->getName()])) {
            throw new \RuntimeException(sprintf('Expected metadata for class %s to be defined in %s.', $name, $file));
        }

        $config        = $config[$name];
        $classMetadata = new ClassMetadata($name);
        $classMetadata->fileResources[] = $file;
        $classMetadata->fileResources[] = $class->getFileName();

        if (isset($config['relations'])) {
            foreach ($config['relations'] as $relation) {
                $classMetadata->addRelation(new Relation(
                    $relation['rel'],
                    $this->createHref($relation),
                    $this->createEmbedded($relation),
                    isset($relation['attributes']) ? $relation['attributes'] : array(),
                    $this->createExclusion($relation)
                ));
            }
        }

        if (isset($config['relation_providers'])) {
            foreach ($config['relation_providers'] as $relationProvider) {
                $classMetadata->addRelationProvider(new RelationProvider($relationProvider));
            }
        }

        return $classMetadata;
    }

    /**
     * {@inheritdoc}
     */
    protected function getExtension()
    {
        return 'yml';
    }

    private function parseExclusion(array $exclusion)
    {
        return new Exclusion(
            isset($exclusion['groups']) ? $exclusion['groups'] : null,
            isset($exclusion['since_version']) ? $exclusion['since_version'] : null,
            isset($exclusion['until_version']) ? $exclusion['until_version'] : null,
            isset($exclusion['max_depth']) ? $exclusion['max_depth'] : null,
            isset($exclusion['exclude_if']) ? $exclusion['exclude_if'] : null
        );
    }

    private function createHref($relation)
    {
        $href = null;
        if (isset($relation['href']) && is_array($href = $relation['href']) && isset($href['route'])) {
            $href = new Route(
                $href['route'],
                isset($href['parameters']) ? $href['parameters'] : array(),
                isset($href['absolute'])   ? $href['absolute'] : false,
                isset($href['generator'])  ? $href['generator'] : null
            );
        }

        return $href;
    }

    private function createEmbedded($relation)
    {
        $embedded = null;
        if (isset($relation['embedded'])) {
            $embedded = $relation['embedded'];

            if (is_array($embedded)) {
                $embeddedExclusion = null;
                if (isset($embedded['exclusion'])) {
                    $embeddedExclusion = $this->parseExclusion($embedded['exclusion']);
                }

                $xmlElementName = isset($embedded['xmlElementName']) ? $embedded['xmlElementName'] : null;
                $embedded       = new Embedded($embedded['content'], $xmlElementName, $embeddedExclusion);
            }
        }

        return $embedded;
    }

    private function createExclusion($relation)
    {
        $exclusion = null;
        if (isset($relation['exclusion'])) {
            $exclusion = $this->parseExclusion($relation['exclusion']);
        }

        return $exclusion;
    }
}
