<?php

declare(strict_types=1);

namespace Hateoas\Serializer;

use Hateoas\Model\Embedded;
use JMS\Serializer\Exception\NotAcceptableException;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Visitor\SerializationVisitorInterface;
use JMS\Serializer\XmlSerializationVisitor;

use function is_bool;

class XmlHalSerializer implements SerializerInterface
{
    public function serializeLinks(array $links, SerializationVisitorInterface $visitor, SerializationContext $context): void
    {
        foreach ($links as $link) {
            if ('self' === $link->getRel()) {
                foreach ($link->getAttributes() as $key => $value) {
                    $visitor->getCurrentNode()->setAttribute($key, $this->formatValue($value));
                }

                $visitor->getCurrentNode()->setAttribute('href', $link->getHref());

                continue;
            }

            $linkNode = $visitor->getDocument()->createElement('link');
            $visitor->getCurrentNode()->appendChild($linkNode);

            $linkNode->setAttribute('rel', $link->getRel());
            $linkNode->setAttribute('href', $link->getHref());

            foreach ($link->getAttributes() as $attributeName => $attributeValue) {
                $linkNode->setAttribute($attributeName, $this->formatValue($attributeValue));
            }
        }
    }

    /**
     * @param Embedded[] $embeddeds
     */
    public function serializeEmbeddeds(array $embeddeds, SerializationVisitorInterface $visitor, SerializationContext $context): void
    {
        foreach ($embeddeds as $embedded) {
            if ($embedded->getData() instanceof \Traversable || is_array($embedded->getData())) {
                foreach ($embedded->getData() as $data) {
                    $entryNode = $visitor->getDocument()->createElement('resource');

                    $visitor->getCurrentNode()->appendChild($entryNode);
                    $visitor->setCurrentNode($entryNode);
                    $visitor->getCurrentNode()->setAttribute('rel', $embedded->getRel());

                    $this->acceptDataAndAppend($embedded, $data, $visitor, $context, null);

                    $visitor->revertCurrentNode();
                }

                continue;
            }

            $entryNode = $visitor->getDocument()->createElement('resource');

            $visitor->getCurrentNode()->appendChild($entryNode);
            $visitor->setCurrentNode($entryNode);
            $visitor->getCurrentNode()->setAttribute('rel', $embedded->getRel());

            $this->acceptDataAndAppend($embedded, $embedded->getData(), $visitor, $context, $embedded->getType());

            $visitor->revertCurrentNode();
        }
    }

    /**
     * @param mixed $data
     */
    private function acceptDataAndAppend(Embedded $embedded, $data, XmlSerializationVisitor $visitor, SerializationContext $context, ?array $type): void
    {
        $context->pushPropertyMetadata($embedded->getMetadata());
        $navigator = $context->getNavigator();
        try {
            if (null !== $node = $navigator->accept($data, $type, $context)) {
                $visitor->getCurrentNode()->appendChild($node);
            }
        } catch (NotAcceptableException $e) {
        }

        $context->popPropertyMetadata();
    }

    /**
     * @param mixed $attributeValue
     */
    private function formatValue($attributeValue): string
    {
        if (is_bool($attributeValue)) {
            return $attributeValue ? 'true' : 'false';
        }

        return (string) $attributeValue;
    }
}
