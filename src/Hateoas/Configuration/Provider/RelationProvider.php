<?php

namespace Hateoas\Configuration\Provider;

use Hateoas\Configuration\Metadata\ClassMetadataInterface;
use Metadata\MetadataFactoryInterface;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class RelationProvider
{
    /**
     * @var MetadataFactoryInterface
     */
    private $metadataFactory;

    /**
     * @var RelationProviderProviderInterface
     */
    private $relationProviderProvider;

    public function __construct(
        MetadataFactoryInterface $metadataFactory,
        RelationProviderProviderInterface $relationProviderProvider
    ) {
        $this->metadataFactory = $metadataFactory;
        $this->relationProviderProvider = $relationProviderProvider;
    }

    public function getRelations($object)
    {
        $classMetadata = $this->metadataFactory->getMetadataForClass(get_class($object));

        if (!$classMetadata instanceof ClassMetadataInterface) {
            return array();
        }

        $relations = array();
        foreach ($classMetadata->getRelationProviders() as $relationProvider) {
            $relationProviderCallable = $this->relationProviderProvider->get($relationProvider, $object);

            if (null === $relationProviderCallable) {
                continue;
            }

            if (!is_callable($relationProviderCallable)) {
                throw new \RuntimeException('The returned relation provider is not callable, it should be.');
            }

            $newRelations = call_user_func_array(
                $relationProviderCallable,
                array(
                    $object,
                    $classMetadata
                )
            );

            if (is_array($newRelations)) {
                $relations = array_merge($relations, $newRelations);
            }
        }

        return $relations;
    }
}
