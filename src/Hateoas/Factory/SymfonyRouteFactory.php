<?php

namespace Hateoas\Factory;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class SymfonyRouteFactory implements RouteFactoryInterface
{
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function create($name, array $parameters, $absolute = false)
    {
        return $this->urlGenerator->generate($name, $parameters, $absolute);
    }
}
