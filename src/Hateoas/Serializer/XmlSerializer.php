<?php

namespace Hateoas\Serializer;

use Hateoas\Model\Embed;
use Hateoas\Model\Resource;
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
    public function serializeLinks(array $links, XmlSerializationVisitor $visitor)
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
    public function serializeEmbedded(array $embeds, XmlSerializationVisitor $visitor, SerializationContext $context)
    {
        foreach ($embeds as $embed) {
            $entryNode = $visitor->getDocument()->createElement($this->getElementName($embed->getData(), $embed));
            $visitor->getCurrentNode()->appendChild($entryNode);
            $visitor->setCurrentNode($entryNode);

            $visitor->getCurrentNode()->setAttribute('rel', $embed->getRel());

            if ($embed->getData() instanceof \Traversable || is_array($embed->getData())) {
                foreach ($embed->getData() as $entry) {
                    $entryNode = $visitor->getDocument()->createElement($this->getElementName($entry));
                    $visitor->getCurrentNode()->appendChild($entryNode);
                    $visitor->setCurrentNode($entryNode);

                    if (null !== $node = $context->accept($entry)) {
                        $visitor->getCurrentNode()->appendChild($node);
                    }

                    $visitor->revertCurrentNode();
                }
            } else {
                $node = $context->accept($embed->getData());
                if (null !== $node) {
                    $visitor->getCurrentNode()->appendChild($node);
                }
            }

            $visitor->revertCurrentNode();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function serializeResource(Resource $resource, XmlSerializationVisitor $visitor, SerializationContext $context)
    {
        if (null === $visitor->getDocument()) {
            if ($visitor->hasDefaultRootName() && null !== $resource->getXmlRootName()) {
                $visitor->setDefaultRootName($resource->getXmlRootName());
            }

            $visitor->document = $visitor->createDocument();
        }

        foreach ($resource->getData() as $key => $value) {
            $entryNode = $visitor->getDocument()->createElement($key);
            $visitor->getCurrentNode()->appendChild($entryNode);
            $visitor->setCurrentNode($entryNode);

            $node = $context->accept($value);
            if (null !== $node) {
                $visitor->getCurrentNode()->appendChild($node);
            }

            $visitor->revertCurrentNode();
        }

        $this->serializeLinks($resource->getLinks(), $visitor);
        $this->serializeEmbedded($resource->getEmbeds(), $visitor, $context);
    }

    private function getElementName($data, Embed $embed = null)
    {
        $elementName = null;

        if (null !== $embed) {
            $elementName = $embed->getXmlElementName();
        }

        if (null == $elementName && is_object($data)) {
            $metadata = $this->metadataFactory->getMetadataForClass(ClassUtils::getClass($data));
            $elementName = $metadata->xmlRootName;
        }

        return $elementName ?: 'entry';
    }
}
