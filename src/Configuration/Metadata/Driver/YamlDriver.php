<?php

declare(strict_types=1);

namespace Hateoas\Configuration\Metadata\Driver;

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
use Metadata\Driver\AbstractFileDriver;
use Metadata\Driver\FileLocatorInterface;
use Symfony\Component\Yaml\Yaml;

class YamlDriver extends AbstractFileDriver
{
    use CheckExpressionTrait;

    /**
     * @var RelationProviderInterface
     */
    private $relationProvider;

    /**
     * @var ParserInterface
     */
    private $typeParser;

    public function __construct(
        FileLocatorInterface $locator,
        CompilableExpressionEvaluatorInterface $expressionLanguage,
        RelationProviderInterface $relationProvider,
        ParserInterface $typeParser
    ) {
        parent::__construct($locator);

        $this->relationProvider = $relationProvider;
        $this->expressionLanguage = $expressionLanguage;
        $this->typeParser = $typeParser;
    }

    protected function loadMetadataFromFile(\ReflectionClass $class, string $file): ?JMSClassMetadata
    {
        $config = Yaml::parse(file_get_contents($file));

        if (!isset($config[$name = $class->getName()])) {
            throw new \RuntimeException(sprintf('Expected metadata for class %s to be defined in %s.', $name, $file));
        }

        $config = $config[$name];
        $classMetadata = new ClassMetadata($name);
        $classMetadata->fileResources[] = $file;
        $classMetadata->fileResources[] = $class->getFileName();

        if (isset($config['relations'])) {
            foreach ($config['relations'] as $relation) {
                $classMetadata->addRelation(new Relation(
                    $relation['rel'],
                    $this->createHref($relation),
                    $this->createEmbedded($relation),
                    isset($relation['attributes']) ? $this->checkExpressionArray($relation['attributes']) : [],
                    $this->createExclusion($relation)
                ));
            }
        }

        if (isset($config['relation_providers'])) {
            foreach ($config['relation_providers'] as $relationProvider) {
                $relations = $this->relationProvider->getRelations(new RelationProvider($relationProvider), $class->getName());
                foreach ($relations as $relation) {
                    $classMetadata->addRelation($relation);
                }
            }
        }

        return $classMetadata;
    }

    protected function getExtension(): string
    {
        return 'yml';
    }

    private function parseExclusion(array $exclusion): Exclusion
    {
        return new Exclusion(
            $exclusion['groups'] ?? null,
            isset($exclusion['since_version']) ? (string) $exclusion['since_version'] : null,
            isset($exclusion['until_version']) ? (string) $exclusion['until_version'] : null,
            isset($exclusion['max_depth']) ? (int) $exclusion['max_depth'] : null,
            isset($exclusion['exclude_if']) ? $this->checkExpression((string) $exclusion['exclude_if']) : null
        );
    }

    /**
     * @param mixed $relation
     *
     * @return Expression|mixed
     */
    private function createHref($relation)
    {
        $href = null;
        if (isset($relation['href']) && is_array($href = $relation['href']) && isset($href['route'])) {
            $absolute = false;
            if (isset($href['absolute']) && is_bool($href['absolute'])) {
                $absolute = $href['absolute'];
            } elseif (isset($href['absolute'])) {
                $absolute = isset($href['absolute']) ? $this->checkExpression($href['absolute']) : false;
            }

            return new Route(
                $this->checkExpression($href['route']),
                isset($href['parameters']) ? (is_array($href['parameters']) ? $this->checkExpressionArray($href['parameters']) : $this->checkExpression($href['parameters'])) : [],
                $absolute,
                $href['generator'] ?? null
            );
        } elseif (isset($relation['href']) && is_string($relation['href'])) {
            $href = $relation['href'];
        }

        return $this->checkExpression($href);
    }

    /**
     * @param mixed $relation
     *
     * @return Embedded|Expression|mixed|null
     */
    private function createEmbedded($relation)
    {
        $embedded = null;
        if (isset($relation['embedded'])) {
            $embedded = $this->checkExpression($relation['embedded']);

            if (is_array($embedded)) {
                $embeddedExclusion = null;
                if (isset($embedded['exclusion'])) {
                    $embeddedExclusion = $this->parseExclusion($embedded['exclusion']);
                }

                $xmlElementName = isset($embedded['xmlElementName']) ? $this->checkExpression((string) $embedded['xmlElementName']) : null;

                return new Embedded(
                    $this->checkExpression($embedded['content']),
                    $xmlElementName,
                    $embeddedExclusion,
                    isset($embedded['type']) ? $this->typeParser->parse($embedded['type']) : null
                );
            }
        }

        return $embedded;
    }

    /**
     * @param mixed $relation
     */
    private function createExclusion($relation): ?Exclusion
    {
        $exclusion = null;
        if (isset($relation['exclusion'])) {
            $exclusion = $this->parseExclusion($relation['exclusion']);
        }

        return $exclusion;
    }
}
