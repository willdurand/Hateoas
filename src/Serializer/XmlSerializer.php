<?php

declare(strict_types=1);

namespace Hateoas\Serializer;

use Hateoas\Model\Embedded;
use Hateoas\Model\Link;
use Hateoas\Util\ClassUtils;
use JMS\Serializer\Exception\NotAcceptableException;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Visitor\SerializationVisitorInterface;
use JMS\Serializer\XmlSerializationVisitor;

use function is_bool;

class XmlSerializer implements SerializerInterface
{
    /**
     * @param Link[]                  $links
     */
    public function serializeLinks(array $links, SerializationVisitorInterface $visitor, SerializationContext $context): void
    {
        foreach ($links as $link) {
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
            $entryNode = $visitor->getDocument()->createElement($this->getElementName($context, $embedded->getData(), $embedded));

            $visitor->getCurrentNode()->appendChild($entryNode);
            $visitor->setCurrentNode($entryNode);
            $visitor->getCurrentNode()->setAttribute('rel', $embedded->getRel());

            if ($embedded->getData() instanceof \Traversable || is_array($embedded->getData())) {
                foreach ($embedded->getData() as $entry) {
                    $entryNode = $visitor->getDocument()->createElement($this->getElementName($context, $entry));

                    $visitor->getCurrentNode()->appendChild($entryNode);
                    $visitor->setCurrentNode($entryNode);

                    $this->acceptDataAndAppend($embedded, $entry, $visitor, $context, null);

                    $visitor->revertCurrentNode();
                }
            } else {
                $this->acceptDataAndAppend($embedded, $embedded->getData(), $visitor, $context, $embedded->getType());
            }

            $visitor->revertCurrentNode();
        }
    }

    /**
     * @param mixed $data
     */
    private function getElementName(SerializationContext $context, $data, ?Embedded $embedded = null): string
    {
        $elementName = null;

        if (null !== $embedded) {
            $elementName = $embedded->getXmlElementName();
        }

        if (null === $elementName && is_object($data)) {
            $metadata    = $context->getMetadataFactory()->getMetadataForClass(ClassUtils::getClass($data));
            $elementName = $metadata->xmlRootName;
        }

        return $elementName ?: 'entry';
    }

    /**
     * @param mixed $data
     */
    private function acceptDataAndAppend(Embedded $embedded, $data, XmlSerializationVisitor $visitor, SerializationContext $context, ?array $type): void
    {
        $context->pushPropertyMetadata($embedded->getMetadata());
        $navigator = $context->getNavigator();
        try {
            if (null !== $node = $navigator->accept($data, $type)) {
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
