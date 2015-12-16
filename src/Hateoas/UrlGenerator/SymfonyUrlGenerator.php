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
        // If is it at least Symfony 2.8 and $absolute is passed as boolean
        if (SymfonyUrlGeneratorInterface::ABSOLUTE_PATH === 1 && is_bool($absolute)) {
            $absolute = $absolute
                ? SymfonyUrlGeneratorInterface::ABSOLUTE_URL
                : SymfonyUrlGeneratorInterface::ABSOLUTE_PATH
            ;
        }

        return $this->urlGenerator->generate($name, $parameters, $absolute);
    }
}
