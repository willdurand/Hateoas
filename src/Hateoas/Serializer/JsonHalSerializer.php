<?php

namespace Hateoas\Serializer;

use JMS\Serializer\JsonSerializationVisitor;
use JMS\Serializer\Metadata\StaticPropertyMetadata;
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

        $visitor->visitProperty(new StaticPropertyMetadata(__CLASS__, '_links', $serializedLinks), $serializedLinks);
    }

    /**
     * {@inheritdoc}
     */
    public function serializeEmbeddeds(array $embeddeds, JsonSerializationVisitor $visitor, SerializationContext $context)
    {
        $serializedEmbeddeds = array();
        $multiple = array();
        $navigator = $context->getNavigator();

        foreach ($embeddeds as $embedded) {
            $context->pushPropertyMetadata($embedded->getMetadata());

            if (!isset($serializedEmbeddeds[$embedded->getRel()])) {
                $serializedEmbeddeds[$embedded->getRel()] =  $navigator->accept($embedded->getData(), null, $context);
            } elseif (!isset($multiple[$embedded->getRel()])) {
                $multiple[$embedded->getRel()] = true;

                $serializedEmbeddeds[$embedded->getRel()] = array(
                    $serializedEmbeddeds[$embedded->getRel()],
                    $navigator->accept($embedded->getData(), null, $context),
                );
            } else {
                $serializedEmbeddeds[$embedded->getRel()][] = $navigator->accept($embedded->getData(), null, $context);
            }

            $context->popPropertyMetadata();
        }

        $visitor->visitProperty(new StaticPropertyMetadata(__CLASS__, '_embedded', $serializedEmbeddeds), $serializedEmbeddeds);
    }
}
