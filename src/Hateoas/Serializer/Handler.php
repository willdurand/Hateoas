<?php

namespace Hateoas\Serializer;

use Hateoas\Collection;
use Hateoas\Resource;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\XmlSerializationVisitor;
use Metadata\MetadataFactoryInterface;

class Handler implements SubscribingHandlerInterface
{
    private $metadataFactory;

    public static function getSubscribingMethods()
    {
        return array(
            array(
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'format'    => 'xml',
                'type'      => 'Hateoas\Resource',
                'method'    => 'serializeResourceToXml',
            ),
            array(
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'format'    => 'xml',
                'type'      => 'Hateoas\Collection',
                'method'    => 'serializeCollectionToXml',
            ),
        );
    }

    public function __construct(MetadataFactoryInterface $metadataFactory)
    {
        $this->metadataFactory = $metadataFactory;
    }

    public function serializeResourceToXml(XmlSerializationVisitor $visitor, Resource $resource, array $type)
    {
        if (null === $visitor->document) {
            $reflClass = new \ReflectionClass(get_class($visitor));
            $reflProp  = $reflClass->getProperty('defaultRootName');
            $reflProp->setAccessible(true);

            if ('result' === $reflProp->getValue($visitor)) {
                if (is_object($resource->getData())
                    && null !== ($m = $this->metadataFactory->getMetadataForClass(get_class($resource->getData())))
                ) {
                    $visitor->setDefaultRootName($m->xmlRootName ?: 'resource');
                }
            }

            $visitor->document = $visitor->createDocument();
        }

        // links
        foreach ($resource->getLinks() as $link) {
            $linkNode = $visitor->getDocument()->createElement('link');
            $visitor->getCurrentNode()->appendChild($linkNode);
            $visitor->setCurrentNode($linkNode);

            if (null !== $node = $visitor->getNavigator()->accept($link, null, $visitor)) {
                $visitor->getCurrentNode()->appendChild($node);
            }

            $visitor->revertCurrentNode();
        }

        // inline data
        return $visitor->getNavigator()->accept($resource->getData(), null, $visitor);
    }

    public function serializeCollectionToXml(XmlSerializationVisitor $visitor, Collection $collection, array $type)
    {
        if (null === $visitor->document) {
            $reflClass = new \ReflectionClass(get_class($visitor));
            $reflProp  = $reflClass->getProperty('defaultRootName');
            $reflProp->setAccessible(true);

            if ('result' === $reflProp->getValue($visitor)) {
                $visitor->setDefaultRootName('resources');
            }

            $visitor->document = $visitor->createDocument();
        }

        // attributes
        foreach (array('total', 'page', 'limit') as $attr) {
            if ($value = $collection->{'get' . ucfirst($attr)}()) {
                $visitor->getCurrentNode()->setAttribute($attr, $value);
            }
        }

        // links
        foreach ($collection->getLinks() as $link) {
            $linkNode = $visitor->getDocument()->createElement('link');
            $visitor->getCurrentNode()->appendChild($linkNode);
            $visitor->setCurrentNode($linkNode);

            if (null !== $node = $visitor->getNavigator()->accept($link, null, $visitor)) {
                $visitor->getCurrentNode()->appendChild($node);
            }

            $visitor->revertCurrentNode();
        }

        // resources
        foreach ($collection->getResources() as $resource) {
            if (is_object($resource->getData())
                && null !== ($m = $this->metadataFactory->getMetadataForClass(get_class($resource->getData())))
            ) {
                $elementName = $m->xmlRootName ?: 'resource';
            }

            $entryNode = $visitor->getDocument()->createElement($elementName);
            $visitor->getCurrentNode()->appendChild($entryNode);
            $visitor->setCurrentNode($entryNode);

            if (null !== $node = $visitor->getNavigator()->accept($resource, null, $visitor)) {
                $visitor->getCurrentNode()->appendChild($node);
            }

            $visitor->revertCurrentNode();
        }
    }
}
