<?php

namespace Hateoas\Configuration\Metadata\Driver;

use Hateoas\Configuration\Embed;
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

        if (isset($config['relations'])) {
            foreach ($config['relations'] as $relation) {
                $name = $relation['rel'];
                $href = null;

                if (isset($relation['href']) && is_array($href = $relation['href']) && isset($href['route'])) {
                    $href = new Route(
                        $href['route'],
                        $href['parameters'],
                        isset($href['absolute']) ? $href['absolute'] : false
                    );
                }

                $embed = null;
                if (isset($relation['embed'])) {
                    $embed = $relation['embed'];

                    if (is_array($embed)) {
                        $embedExclusion = null;
                        if (isset($embed['exclusion'])) {
                            $embedExclusion = $this->parseExclusion($embed['exclusion']);
                        }

                        $xmlElementName = isset($embed['xmlElementName']) ? $embed['xmlElementName'] : null;
                        $embed          = new Embed($embed['content'], $xmlElementName, $embedExclusion);
                    }
                }

                $attributes = isset($relation['attributes']) ? $relation['attributes'] : array();

                $exclusion = null;
                if (isset($relation['exclusion'])) {
                    $exclusion = $this->parseExclusion($relation['exclusion']);
                }

                $classMetadata->addRelation(new Relation(
                    $name,
                    $href,
                    $embed,
                    $attributes,
                    $exclusion
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
}
