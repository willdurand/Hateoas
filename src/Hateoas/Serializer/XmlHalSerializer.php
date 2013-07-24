<?php

namespace Hateoas\Serializer;

use Hateoas\Model\Resource;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\XmlSerializationVisitor;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class XmlHalSerializer implements XmlSerializerInterface
{
    /**
     * {@inheritdoc}
     */
    public function serializeLinks(array $links, XmlSerializationVisitor $visitor)
    {
        foreach ($links as $link) {
            if ('self' === $link->getRel()) {
                $visitor->getCurrentNode()->setAttribute('href', $link->getHref());
                // todo what about this link attributes ?

                continue;
            }

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
            if ($embed->getData() instanceof \Traversable || is_array($embed->getData())) {
                foreach ($embed->getData() as $data) {
                    $entryNode = $visitor->getDocument()->createElement('resource');
                    $visitor->getCurrentNode()->appendChild($entryNode);
                    $visitor->setCurrentNode($entryNode);
                    $visitor->getCurrentNode()->setAttribute('rel', $embed->getRel());

                    $node = $context->accept($data);
                    if (null !== $node) {
                        $visitor->getCurrentNode()->appendChild($node);
                    }

                    $visitor->revertCurrentNode();
                }

                continue;
            }

            $entryNode = $visitor->getDocument()->createElement('resource');
            $visitor->getCurrentNode()->appendChild($entryNode);
            $visitor->setCurrentNode($entryNode);

            $visitor->getCurrentNode()->setAttribute('rel', $embed->getRel());

            $node = $context->accept($embed->getData());
            if (null !== $node) {
                $visitor->getCurrentNode()->appendChild($node);
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
            if ($visitor->hasDefaultRootName()) {
                $visitor->setDefaultRootName('resource');
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
}
