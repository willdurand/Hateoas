<?php

namespace Hateoas\Serializer;
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
}
