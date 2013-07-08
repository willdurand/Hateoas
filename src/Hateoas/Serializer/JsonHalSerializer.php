<?php

namespace Hateoas\Serializer;

use Hateoas\Model\Resource;
use JMS\Serializer\JsonSerializationVisitor;
use JMS\Serializer\SerializationContext;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class JsonHalSerializer implements JsonSerializerInterface
{
    /**
     * {@inheritdoc}
     */
    public function serializeLinks(array $links, JsonSerializationVisitor $visitor)
    {
        $visitor->addData('_links', $this->createSerializedLinks($links));
    }

    /**
     * {@inheritdoc}
     */
    public function serializeEmbedded(array $embeddedMap, JsonSerializationVisitor $visitor, SerializationContext $context)
    {
        $visitor->addData('_embedded', $context->accept($embeddedMap));
    }

    /**
     * {@inheritdoc}
     */
    public function serializeResource(Resource $resource, JsonSerializationVisitor $visitor, SerializationContext $context)
    {
        $addRoot = false;
        if (null === $visitor->getRoot()) {
            $addRoot = true;
        }

        $result = $resource->getData();

        if (count($resource->getLinks()) > 0) {
            $result['_links'] = $this->createSerializedLinks($resource->getLinks());
        }

        if (count($resource->getEmbedded()) > 0) {
            $result['_embedded'] = $context->accept($resource->getEmbedded());
        }

        if ($addRoot) {
            $visitor->setRoot($result);
        }

        return $result;
    }

    private function createSerializedLinks(array $links)
    {
        $serializedLinks = array();

        foreach ($links as $link) {
            $serializedLink = array(
                'href' => $link->getHref(),
            );
            $serializedLink = array_merge($serializedLink, $link->getAttributes());

            if (!isset($serializedLinks[$link->getRel()])) {
                $serializedLinks[$link->getRel()] = $serializedLink;
            } else if (isset($serializedLinks[$link->getRel()]['href'])) {
                $serializedLinks[$link->getRel()] = array(
                    $serializedLinks[$link->getRel()],
                    $serializedLink
                );
            } else {
                $serializedLinks[$link->getRel()][] = $serializedLink;
            }
        }

        return $serializedLinks;
    }
}
