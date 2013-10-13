<?php

namespace Hateoas\Configuration\Provider\Resolver;

use Hateoas\Configuration\RelationProvider as RelationProviderConfiguration;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class SymfonyContainerResolver implements RelationProviderResolverInterface
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
    public function getRelationProvider(RelationProviderConfiguration $configuration, $object)
    {
        if (!preg_match('/^(?P<service>[a-z0-9_.]+):(?P<method>[a-z0-9_]+)$/i', $configuration->getName(), $matches)) {
            return null;
        }

        return array(
            $this->container->get($matches['service']),
            $matches['method']
        );
    }
}
