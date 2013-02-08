<?php

namespace Hateoas\Builder;

use Hateoas\Link;
use Hateoas\Factory\Definition\RouteLinkDefinition;
use Hateoas\Factory\Definition\LinkDefinition;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * @author William Durand <william.durand1@gmail.com>
 */
class LinkBuilder implements LinkBuilderInterface
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
    public function createFromDefinition(LinkDefinition $definition, $data)
    {
        if (!$definition instanceof RouteLinkDefinition) {
            return;
        }

        $parameters = array();
        $accessor   = PropertyAccess::getPropertyAccessor();

        foreach ($definition->getParameters() as $name => $path) {
            if (is_numeric($name)) {
                $name = $path;
            }

            $parameters[$name] = $accessor->getValue($data, $path);
        }

        return $this->create(
            $definition->getRoute(),
            $parameters,
            $definition->getRel(),
            $definition->getType()
        );
    }

    /**
     * @param string $route
     * @param array  $parameters
     * @param string $rel
     * @param string $type
     *
     * @return Link
     */
    public function create($route, array $parameters = array(), $rel = Link::REL_SELF, $type = null)
    {
        $url = $this->urlGenerator->generate($route, $parameters, true);

        return new Link($url, $rel, $type);
    }
}
