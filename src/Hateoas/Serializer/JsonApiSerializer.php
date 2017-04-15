<?php

namespace Hateoas\Serializer;

use JMS\Serializer\JsonSerializationVisitor;
use JMS\Serializer\SerializationContext;

/**
 * @author William Durand <william.durand1@gmail.com>
 */
class JsonApiSerializer implements JsonSerializerInterface
{
    /**
     * {@inheritdoc}
     */
    public function serializeLinks(array $links, JsonSerializationVisitor $visitor, SerializationContext $context)
    {
        $serializedLinks = array();
        $topLevelSerializedLinks = array();

        foreach ($links as $link) {
            $serializedLink = array_merge(array(
                'href' => $link->getHref(),
            ), $link->getAttributes());

            if (isset($serializedLink['topLevel']) && true === $serializedLink['topLevel']) {
                unset($serializedLink['topLevel']);
                $topLevelSerializedLinks[$link->getRel()] = $serializedLink;

                continue;
            }

            if (!isset($serializedLinks[$link->getRel()])) {
                $serializedLinks[$link->getRel()] = $serializedLink['href'];
            } else {
                $serializedLinks[$link->getRel()][] = $serializedLink['href'];
            }
        }

        $visitor->addData('links', $serializedLinks);
        $visitor->setRoot(array('links' => $topLevelSerializedLinks));
    }

    /**
     * {@inheritdoc}
     */
    public function serializeEmbeds(array $embeds, JsonSerializationVisitor $visitor, SerializationContext $context)
    {
        $serializedEmbeds = array();
        foreach ($embeds as $embed) {
            $serializedEmbeds[$embed->getRel()] = $context->accept($embed->getData());
        }

        $visitor->setRoot(array('linked' => $serializedEmbeds));
    }
}
