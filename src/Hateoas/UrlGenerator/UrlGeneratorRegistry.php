<?php

namespace Hateoas\UrlGenerator;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class UrlGeneratorRegistry
{
    const DEFAULT_URL_GENERATOR_KEY = 'default';

    private $urlGenerators;

    public function __construct(UrlGeneratorInterface $defaultUrlGenerator = null)
    {
        $this->urlGenerators = array();

        if (null !== $defaultUrlGenerator) {
            $this->urlGenerators = array(
                self::DEFAULT_URL_GENERATOR_KEY => $defaultUrlGenerator,
            );
        }
    }

    /**
     * @param string|null $name If null it will return the default url generator
     *
     * @return UrlGeneratorInterface
     */
    public function get($name = null)
    {
        if (null === $name) {
            $name = self::DEFAULT_URL_GENERATOR_KEY;
        }

        if (!isset($this->urlGenerators[$name])) {
            throw new \InvalidArgumentException(
                sprintf(
                    'The "%s" url generator is not set. Available url generators are: %s.',
                    $name,
                    join(', ', array_keys($this->urlGenerators))
                )
            );
        }

        return $this->urlGenerators[$name];
    }

    /**
     * @param string|null           $name
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function set($name, UrlGeneratorInterface $urlGenerator)
    {
        if (null === $name) {
            $name = self::DEFAULT_URL_GENERATOR_KEY;
        }

        $this->urlGenerators[$name] = $urlGenerator;
    }

    /**
     * @return boolean
     */
    public function hasGenerators()
    {
        return count($this->urlGenerators) > 0;
    }
}
