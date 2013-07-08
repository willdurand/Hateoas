<?php

namespace Hateoas\Configuration\Metadata\Driver;

use Hateoas\Configuration\Metadata\ClassMetadata;
use Hateoas\Configuration\Relation;
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

        $config = $config[$name];

        $classMetadata = new ClassMetadata($name);

        if (isset($config['relations'])) {
            foreach ($config['relations'] as $relation) {
                $name = $relation['rel'];
                $href = $relation['href'];
                if (is_array($href) && isset($href['route'])) {
                    $href = new Route($href['route'], $href['parameters']);
                }
                $embed = null;
                if (isset($relation['embed'])) {
                    $embed = $relation['embed'];
                }
                $attributes = isset($relation['attributes']) ? $relation['attributes'] : array();

                $relation = new Relation($name, $href, $embed, $attributes);
                $classMetadata->addRelation($relation);
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
}
