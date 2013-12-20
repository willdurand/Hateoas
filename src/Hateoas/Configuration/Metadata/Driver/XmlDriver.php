<?php

namespace Hateoas\Configuration\Metadata\Driver;

use Hateoas\Configuration\Embedded;
use Hateoas\Configuration\Exclusion;
use Hateoas\Configuration\Metadata\ClassMetadata;
use Hateoas\Configuration\Relation;
use Hateoas\Configuration\RelationProvider;
use Hateoas\Configuration\Route;
use JMS\Serializer\Exception\XmlErrorException;
use Metadata\Driver\AbstractFileDriver;

/**
 * @author Miha Vrhovnik <miha.vrhovnik@pagein.net>
 */
class XmlDriver extends AbstractFileDriver
{
    const NAMESPACE_URI = 'https://github.com/willdurand/Hateoas';

    /**
     * {@inheritdoc}
     */
    protected function loadMetadataFromFile(\ReflectionClass $class, $file)
    {
        $previous = libxml_use_internal_errors(true);
        $root     = simplexml_load_file($file);
        libxml_use_internal_errors($previous);

        if (false === $root) {
            throw new XmlErrorException(libxml_get_last_error());
        }

        $name = $class->getName();
        if (!$exists = $root->xpath("./class[@name = '" . $name . "']")) {
            throw new \RuntimeException(sprintf('Expected metadata for class %s to be defined in %s.', $name, $file));
        }

        $classMetadata = new ClassMetadata($name);
        $classMetadata->fileResources[] = $file;
        $classMetadata->fileResources[] = $class->getFileName();

        if ($exists[0]->attributes(self::NAMESPACE_URI)->providers) {
            $providers = preg_split('/\s*,\s*/', (string) $exists[0]->attributes(self::NAMESPACE_URI)->providers);

            foreach ($providers as $relationProvider) {
                $classMetadata->addRelationProvider(new RelationProvider($relationProvider));
            }
        }

        $elements = $exists[0]->children(self::NAMESPACE_URI);

        foreach ($elements->relation as $relation) {
            $name = (string) $relation->attributes('')->rel;

            $href = null;
            if (isset($relation->href)) {
                $href = $this->createHref($relation->href, $name);
            }

            $embedded = null;
            if (isset($relation->embedded)) {
                $embedded = $this->createEmbedded($relation->embedded);
            }

            $attributes = array();
            foreach ($relation->attribute as $attribute) {
                $attributes[(string) $attribute->attributes('')->name] = (string) $attribute->attributes('')->value;
            }

            $exclusion = isset($relation->exclusion) ? $this->parseExclusion($relation->exclusion) : null;

            $classMetadata->addRelation(
                new Relation(
                    $name,
                    $href,
                    $embedded,
                    $attributes,
                    $exclusion
                )
            );
        }

        return $classMetadata;
    }

    /**
     * {@inheritdoc}
     */
    protected function getExtension()
    {
        return 'xml';
    }

    private function parseExclusion(\SimpleXMLElement $exclusion)
    {
        return new Exclusion(
            isset($exclusion->attributes('')->groups) ? preg_split('/\s*,\s*/', (string) $exclusion->attributes('')->groups) : null,
            isset($exclusion->attributes('')->{'since-version'}) ? (string) $exclusion->attributes('')->{'since-version'} : null,
            isset($exclusion->attributes('')->{'until-version'}) ? (string) $exclusion->attributes('')->{'until-version'} : null,
            isset($exclusion->attributes('')->{'max-depth'}) ? (string) $exclusion->attributes('')->{'max-depth'} : null,
            isset($exclusion->attributes('')->{'exclude-if'}) ? (string) $exclusion->attributes('')->{'exclude-if'} : null
        );
    }

    private function createHref($href, $name)
    {
        if (isset($href->attributes('')->uri) && isset($href->attributes('')->route)) {
            throw new \RuntimeException(sprintf(
                'uri and route attributes are mutually exclusive, please set only one of them. The problematic relation rel is %s.',
                $name
            ));
        } elseif (isset($href->attributes('')->uri)) {
            $href = (string) $href->attributes('')->uri;
        } else {
            $parameters = array();
            foreach ($href->parameter as $parameter) {
                $parameters[(string) $parameter->attributes('')->name] = (string) $parameter->attributes('')->value;
            }

            $href = new Route(
                (string) $href->attributes('')->route,
                $parameters,
                null !== ($absolute = $href->attributes('')->absolute) ? 'true' === strtolower($absolute) : false,
                isset($href->attributes('')->generator) ? (string) $href->attributes('')->generator : null
            );
        }

        return $href;
    }

    private function createEmbedded($embedded)
    {
        $embeddedExclusion = isset($embedded->exclusion) ? $this->parseExclusion($embedded->exclusion) : null;
        $xmlElementName = isset($embedded->attributes('')->{'xml-element-name'}) ? (string) $embedded->attributes('')->{'xml-element-name'} : null;

        return new Embedded(
            (string) $embedded->content,
            $xmlElementName,
            $embeddedExclusion
        );
    }
}
