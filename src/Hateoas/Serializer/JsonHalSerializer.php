<?php

namespace Hateoas\Serializer;

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

        $visitor->addData('_links', $serializedLinks);
    }

    /**
     * {@inheritdoc}
     */
    public function serializeEmbeddedMap(\SplObjectStorage $embeddedMap, JsonSerializationVisitor $visitor, SerializationContext $context)
    {
        $serializedEmbedded = array();

        foreach ($embeddedMap as $relation) {
            $data = $embeddedMap->offsetGet($relation);
            $serializedEmbedded[$relation->getName()] = $context->accept($data);
        }

        $visitor->addData('_embedded', $serializedEmbedded);
    }
}
