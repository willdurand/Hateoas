<?php

declare(strict_types=1);

namespace Hateoas\UrlGenerator;

class UrlGeneratorRegistry
{
    public const DEFAULT_URL_GENERATOR_KEY = 'default';

    /**
     * @var UrlGeneratorInterface[]
     */
    private $urlGenerators;

    public function __construct(?UrlGeneratorInterface $defaultUrlGenerator = null)
    {
        $this->urlGenerators = [];

        if (null !== $defaultUrlGenerator) {
            $this->urlGenerators = [self::DEFAULT_URL_GENERATOR_KEY => $defaultUrlGenerator];
        }
    }

    /**
     * @param string|null $name If null it will return the default url generator
     */
    public function get(?string $name = null): UrlGeneratorInterface
    {
        if (null === $name) {
            $name = self::DEFAULT_URL_GENERATOR_KEY;
        }

        if (!isset($this->urlGenerators[$name])) {
            throw new \InvalidArgumentException(
                sprintf(
                    'The "%s" url generator is not set. Available url generators are: %s.',
                    $name,
                    implode(', ', array_keys($this->urlGenerators))
                )
            );
        }

        return $this->urlGenerators[$name];
    }

    public function set(?string $name, UrlGeneratorInterface $urlGenerator): void
    {
        if (null === $name) {
            $name = self::DEFAULT_URL_GENERATOR_KEY;
        }

        $this->urlGenerators[$name] = $urlGenerator;
    }

    public function hasGenerators(): bool
    {
        return count($this->urlGenerators) > 0;
    }
}
