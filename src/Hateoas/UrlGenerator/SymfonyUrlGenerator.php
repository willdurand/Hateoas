<?php

declare(strict_types=1);

namespace Hateoas\UrlGenerator;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface as SymfonyUrlGeneratorInterface;

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
     * @param bool|int $absolute
     */
    public function generate(string $name, array $parameters, $absolute = false): string
    {
        // If is it at least Symfony 2.8 and $absolute is passed as boolean
        if (1 === SymfonyUrlGeneratorInterface::ABSOLUTE_PATH && is_bool($absolute)) {
            $absolute = $absolute
                ? SymfonyUrlGeneratorInterface::ABSOLUTE_URL
                : SymfonyUrlGeneratorInterface::ABSOLUTE_PATH;
        }

        return $this->urlGenerator->generate($name, $parameters, $absolute);
    }
}
