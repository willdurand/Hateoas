<?php

namespace Hateoas\Serializer;

use Hateoas\Model\Embedded;
use Hateoas\Model\Link;
use Hateoas\Util\ClassUtils;
use JMS\Serializer\Exception\NotAcceptableException;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Visitor\SerializationVisitorInterface;
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
     * @param Link[]                  $links
     * @param XmlSerializationVisitor $visitor
     * @param SerializationContext    $context
     */
    public function serializeLinks(array $links, SerializationVisitorInterface $visitor, SerializationContext $context)
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
     * @param Embedded[]              $embeddeds
     * @param XmlSerializationVisitor $visitor
     * @param SerializationContext    $context
     */
    public function serializeEmbeddeds(array $embeddeds, SerializationVisitorInterface $visitor, SerializationContext $context)
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

                    $this->acceptDataAndAppend($embedded, $entry, $visitor, $context);

                    $visitor->revertCurrentNode();
                }
            } else {
                $this->acceptDataAndAppend($embedded, $embedded->getData(), $visitor, $context);
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

    private function acceptDataAndAppend(Embedded $embedded, $data, XmlSerializationVisitor $visitor, SerializationContext $context)
    {
        $context->pushPropertyMetadata($embedded->getMetadata());
        $navigator = $context->getNavigator();
        try {
            if (null !== $node = $navigator->accept($data, null)) {
                $visitor->getCurrentNode()->appendChild($node);
            }
        } catch (NotAcceptableException $e) {

        }
        $context->popPropertyMetadata();
    }
}
