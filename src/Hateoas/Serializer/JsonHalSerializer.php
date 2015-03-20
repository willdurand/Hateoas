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
    public function serializeLinks(array $links, JsonSerializationVisitor $visitor, SerializationContext $context)
    {
        $serializedLinks = array();
        foreach ($links as $link) {
            $serializedLink = array_merge(array(
                'href' => $link->getHref(),
            ), $link->getAttributes());

            if (!isset($serializedLinks[$link->getRel()]) && 'curies' !== $link->getRel()) {
                $serializedLinks[$link->getRel()] = $serializedLink;
            } elseif (isset($serializedLinks[$link->getRel()]['href'])) {
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
    public function serializeEmbeddeds(array $embeddeds, JsonSerializationVisitor $visitor, SerializationContext $context)
    {
        $serializedEmbeddeds = array();
        $multiple = array();
        foreach ($embeddeds as $embedded) {
            if (!isset($serializedEmbeddeds[$embedded->getRel()])) {
                $serializedEmbeddeds[$embedded->getRel()] = $context->accept($embedded->getData());
            } elseif (!isset($multiple[$embedded->getRel()])) {
                $multiple[$embedded->getRel()] = true;

                $serializedEmbeddeds[$embedded->getRel()] = array(
                    $serializedEmbeddeds[$embedded->getRel()],
                    $context->accept($embedded->getData()),
                );
            } else {
                $serializedEmbeddeds[$embedded->getRel()][] = $context->accept($embedded->getData());
            }
        }

        $visitor->addData('_embedded', $serializedEmbeddeds);
    }
}
