<?php

namespace Hateoas\Configuration\Provider;

use Hateoas\Configuration\Metadata\ClassMetadataInterface;
use Hateoas\Configuration\Provider\Resolver\RelationProviderResolverInterface;
use Metadata\MetadataFactoryInterface;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class RelationProvider implements RelationProviderInterface
{
    /**
     * @var MetadataFactoryInterface
     */
    private $metadataFactory;

    /**
     * @var RelationProviderResolverInterface
     */
    private $resolver;

    public function __construct(MetadataFactoryInterface $metadataFactory, RelationProviderResolverInterface $resolver)
    {
        $this->metadataFactory = $metadataFactory;
        $this->resolver        = $resolver;
    }

    public function getRelations($object)
    {
        $classMetadata = $this->metadataFactory->getMetadataForClass(get_class($object));

        if (!$classMetadata instanceof ClassMetadataInterface) {
            return array();
        }

        $relations = array();
        foreach ($classMetadata->getRelationProviders() as $configuration) {
            if (null === $relationProviderCallable = $this->resolver->getRelationProvider($configuration, $object)) {
                continue;
            }

            if (!is_callable($relationProviderCallable)) {
                throw new \RuntimeException('The returned relation provider is not callable, it should be.');
            }

            $newRelations = call_user_func_array(
                $relationProviderCallable,
                array($object, $classMetadata)
            );

            if (is_array($newRelations)) {
                $relations = array_merge($relations, $newRelations);
            }
        }

        return $relations;
    }
}
