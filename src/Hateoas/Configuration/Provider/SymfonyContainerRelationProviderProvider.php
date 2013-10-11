<?php

namespace Hateoas\Configuration\Provider;

use Hateoas\Configuration\RelationProvider as RelationProviderConfiguration;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class SymfonyContainerRelationProviderProvider implements RelationProviderProviderInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function get(RelationProviderConfiguration $relationProvider, $object)
    {
        if (!preg_match('/^(?P<service>[a-z0-9_.]+):(?P<method>[a-z0-9_]+)$/i', $relationProvider->getName(), $matches)) {
            return null;
        }

        return array(
            $this->container->get($matches['service']),
            $matches['method']
        );
    }
}
