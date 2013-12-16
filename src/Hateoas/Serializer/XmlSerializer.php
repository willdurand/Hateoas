<?php

namespace Hateoas\Serializer;

use Hateoas\Model\Embedded;
use Hateoas\Util\ClassUtils;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\XmlSerializationVisitor;
use Metadata\MetadataFactoryInterface;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class XmlSerializer implements XmlSerializerInterface, JMSSerializerMetadataAwareInterface
{
    /**
     * @var MetadataFactoryInterface
     */
    private $metadataFactory;

    /**
     * {@inheritdoc}
     */
    public function setMetadataFactory(MetadataFactoryInterface $metadataFactory)
    {
        $this->metadataFactory = $metadataFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function serializeLinks(array $links, XmlSerializationVisitor $visitor, SerializationContext $context)
    {
        foreach ($links as $link) {
            $linkNode = $visitor->getDocument()->createElement('link');
            $visitor->getCurrentNode()->appendChild($linkNode);

            $linkNode->setAttribute('rel', $link->getRel());
            $linkNode->setAttribute('href', $link->getHref());

            foreach ($link->getAttributes() as $attributeName => $attributeValue) {
                $linkNode->setAttribute($attributeName, $attributeValue);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function serializeEmbeddeds(array $embeddeds, XmlSerializationVisitor $visitor, SerializationContext $context)
    {
        foreach ($embeddeds as $embedded) {
            $entryNode = $visitor->getDocument()->createElement($this->getElementName($embedded->getData(), $embedded));

            $visitor->getCurrentNode()->appendChild($entryNode);
            $visitor->setCurrentNode($entryNode);
            $visitor->getCurrentNode()->setAttribute('rel', $embedded->getRel());

            if ($embedded->getData() instanceof \Traversable || is_array($embedded->getData())) {
                foreach ($embedded->getData() as $entry) {
                    $entryNode = $visitor->getDocument()->createElement($this->getElementName($entry));

                    $visitor->getCurrentNode()->appendChild($entryNode);
                    $visitor->setCurrentNode($entryNode);

                    if (null !== $node = $context->accept($entry)) {
                        $visitor->getCurrentNode()->appendChild($node);
                    }

                    $visitor->revertCurrentNode();
                }
            } elseif (null !== $node = $context->accept($embedded->getData())) {
                $visitor->getCurrentNode()->appendChild($node);
            }

            $visitor->revertCurrentNode();
        }
    }

    private function getElementName($data, Embedded $embedded = null)
    {
        $elementName = null;

        if (null !== $embedded) {
            $elementName = $embedded->getXmlElementName();
        }

        if (null == $elementName && is_object($data)) {
            $metadata    = $this->metadataFactory->getMetadataForClass(ClassUtils::getClass($data));
            $elementName = $metadata->xmlRootName;
        }

        return $elementName ?: 'entry';
    }
}
