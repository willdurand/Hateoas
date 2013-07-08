<?php

namespace Hateoas\Serializer;

use Hateoas\Model\Resource;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\XmlSerializationVisitor;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class XmlSerializer implements XmlSerializerInterface
{
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
    public function serializeEmbedded(array $embeddedMap, XmlSerializationVisitor $visitor, SerializationContext $context)
    {
        foreach ($embeddedMap as $rel => $data) {
            $entryNode = $visitor->getDocument()->createElement('entry'); // TODO use the jms serializer metadata factory to get the xmlrootname...
            $visitor->getCurrentNode()->appendChild($entryNode);
            $visitor->setCurrentNode($entryNode);

            $visitor->getCurrentNode()->setAttribute('rel', $rel);

            $node = $context->accept($data);
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
                //$visitor->setDefaultRootName('resource'); // todo maybe allow Resource to define the rootname
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
        $this->serializeEmbedded($resource->getEmbedded(), $visitor, $context);
    }
}
