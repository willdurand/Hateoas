<?php

namespace Hateoas\Serializer;

use JMS\Serializer\Exception\NotAcceptableException;
use JMS\Serializer\Metadata\StaticPropertyMetadata;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Visitor\SerializationVisitorInterface;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class JsonHalSerializer implements JsonSerializerInterface
{
    /**
     * {@inheritdoc}
     */
    public function serializeLinks(array $links, SerializationVisitorInterface $visitor, SerializationContext $context)
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
    public function serializeEmbeddeds(array $embeddeds, SerializationVisitorInterface $visitor, SerializationContext $context)
    {
        $serializedEmbeddeds = array();
        $multiple = array();
        $navigator = $context->getNavigator();

        foreach ($embeddeds as $embedded) {
            $context->pushPropertyMetadata($embedded->getMetadata());
            try {
                if (!isset($serializedEmbeddeds[$embedded->getRel()])) {
                    $serializedEmbeddeds[$embedded->getRel()] = $navigator->accept($embedded->getData(), null, $context);
                } elseif (!isset($multiple[$embedded->getRel()])) {
                    $multiple[$embedded->getRel()] = true;

                    $serializedEmbeddeds[$embedded->getRel()] = array(
                        $serializedEmbeddeds[$embedded->getRel()],
                        $navigator->accept($embedded->getData(), null, $context),
                    );
                } else {
                    $serializedEmbeddeds[$embedded->getRel()][] = $navigator->accept($embedded->getData(), null, $context);
                }
            } catch (NotAcceptableException $e) {

            }

            $context->popPropertyMetadata();
        }

        $visitor->visitProperty(new StaticPropertyMetadata(__CLASS__, '_embedded', $serializedEmbeddeds), $serializedEmbeddeds);
    }
}
