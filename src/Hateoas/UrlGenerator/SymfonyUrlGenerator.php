<?php

namespace Hateoas\UrlGenerator;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface as SymfonyUrlGeneratorInterface;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class SymfonyUrlGenerator implements UrlGeneratorInterface
{
    /**
     * @var SymfonyUrlGeneratorInterface
     */
    private $urlGenerator;

    public function __construct(SymfonyUrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function generate($name, array $parameters, $absolute = false)
    {
        return $this->urlGenerator->generate($name, $parameters, $absolute);
    }
}
