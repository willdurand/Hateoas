<?php

namespace Hateoas\Serializer;

use Hateoas\Collection;
use Hateoas\Link;
use Hateoas\Resource;
use JMS\Serializer\Context;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\JsonSerializationVisitor;
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
            array(
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'format'    => 'json',
                'type'      => 'Hateoas\Resource',
                'method'    => 'serializeResourceToJson',
            ),
            array(
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'format'    => 'json',
                'type'      => 'Hateoas\Collection',
                'method'    => 'serializeCollectionToJson',
            ),
        );
    }

    public function __construct(MetadataFactoryInterface $metadataFactory)
    {
        $this->metadataFactory = $metadataFactory;
    }

    public function serializeResourceToXml(XmlSerializationVisitor $visitor, Resource $resource, array $type, Context $context)
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

            if (null !== $node = $context->accept($link)) {
                $visitor->getCurrentNode()->appendChild($node);
            }

            $visitor->revertCurrentNode();
        }

        // Embeds
        foreach ($resource->getEmbeds() as $name => $embeds) {
            $embedNode = $visitor->getDocument()->createElement($name);
            $visitor->getCurrentNode()->appendChild($embedNode);
            $visitor->setCurrentNode($embedNode);

            if (is_array($embeds)) {
                foreach ($embeds as $embed) {
                    $elementName = 'resource';
                    if (is_object($embed->getData()) && null !== ($m = $this->metadataFactory->getMetadataForClass(get_class($embed->getData())))) {
                        $elementName = $m->xmlRootName ?: 'resource';
                    }

                    $entryNode = $visitor->getDocument()->createElement($elementName);
                    $visitor->getCurrentNode()->appendChild($entryNode);
                    $visitor->setCurrentNode($entryNode);

                    if (null !== $node = $context->accept($embed)) {
                        $visitor->getCurrentNode()->appendChild($node);
                    }

                    $visitor->revertCurrentNode();
                }
            } else {
                if (null !== $node = $context->accept($embeds)) {
                    $visitor->getCurrentNode()->appendChild($embeds);
                }
            }
            $visitor->revertCurrentNode();
        }

        // form
        foreach ($resource->getForms() as $elementName => $form) {
            $entryNode = $visitor->getDocument()->createElement($elementName);
            $visitor->getCurrentNode()->appendChild($entryNode);
            $visitor->setCurrentNode($entryNode);

            if (null !== $node = $context->accept($form)) {
                $visitor->getCurrentNode()->appendChild($node);
            }

            $visitor->revertCurrentNode();
        }

        // inline data
        return $context->accept($resource->getData());
    }

    public function serializeCollectionToXml(XmlSerializationVisitor $visitor, Collection $collection, array $type, Context $context)
    {
        if (null === $visitor->document) {
            $visitor->setDefaultRootName($collection->getRootName() ?: 'resources');
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

            if (null !== $node = $context->accept($link)) {
                $visitor->getCurrentNode()->appendChild($node);
            }

            $visitor->revertCurrentNode();
        }

        // resources
        foreach ($collection->getResources() as $resource) {
            $elementName = 'resource';
            if (is_object($resource->getData())
                && null !== ($m = $this->metadataFactory->getMetadataForClass(get_class($resource->getData())))
            ) {
                $elementName = $m->xmlRootName ?: 'resource';
            }

            $entryNode = $visitor->getDocument()->createElement($elementName);
            $visitor->getCurrentNode()->appendChild($entryNode);
            $visitor->setCurrentNode($entryNode);

            if (null !== $node = $context->accept($resource)) {
                $visitor->getCurrentNode()->appendChild($node);
            }

            $visitor->revertCurrentNode();
        }

        // form
        foreach ($collection->getForms() as $elementName => $form) {
            $entryNode = $visitor->getDocument()->createElement($elementName);
            $visitor->getCurrentNode()->appendChild($entryNode);
            $visitor->setCurrentNode($entryNode);

            if (null !== $node = $context->accept($form)) {
                $visitor->getCurrentNode()->appendChild($node);
            }

            $visitor->revertCurrentNode();
        }
    }

    public function serializeResourceToJson(JsonSerializationVisitor $visitor, Resource $resource, array $type, Context $context)
    {
        $classMetadata = $this->metadataFactory->getMetadataForClass(get_class($resource));

        // inline
        $data = $context->accept($resource->getData());

        //links
        $linkPropertyMetadata = $classMetadata->propertyMetadata['links'];
        $linksName = $linkPropertyMetadata->serializedName ?: '_links';
        $data[$linksName] = $this->getLinksFrom($resource);

        // embeds
        $EmbedPropertyMetadata = $classMetadata->propertyMetadata['embeds'];
        $embedName = $EmbedPropertyMetadata->serializedName ?: '_embeds';
        foreach ($resource->getEmbeds() as $name => $embeds) {
            if (is_array($embeds)) {
                foreach ($embeds as $embed) {
                    $data[$embedName][$name][] = $this->serializeResourceToJson($visitor, $embed, $type, $context);
                }
            } else {
                $data[$embedName][$name] = $this->serializeResourceToJson($visitor, $embeds, $type, $context);
            }
        }

        $visitor->setRoot($data);

        return $data;
    }

    public function serializeCollectionToJson(JsonSerializationVisitor $visitor, Collection $collection, array $type, Context $context)
    {
        $metadata  = $this->metadataFactory
            ->getMetadataForClass(get_class($collection))
            ->propertyMetadata['links'];
        $linksName = $metadata->serializedName ?: '_links';
        $rootName  = $collection->getRootName() ?: 'resources';

        // attributes
        foreach (array('total', 'page', 'limit', 'offset') as $attr) {
            if (null !== ($value = $collection->{'get' . ucfirst($attr)}())) {
                $data[$attr] = $value;
            }
        }

        // links
        $data[$linksName]  = $this->getLinksFrom($collection);
        // resources
        $data[$rootName] = array();
        foreach ($collection->getResources() as $resource) {
            $data[$rootName][] = $context->accept($resource);
        }

        $visitor->setRoot($data);
    }

    private function getLinksFrom($object)
    {
        $links = array();
        foreach ($object->getLinks() as $link) {
            $data         = array();
            $data['href'] = $link->getHref();

            if (null !== $type = $link->getType()) {
                $data['type'] = $type;
            }

            if (isset($links[$link->getRel()])) {
                // in order to support multiple links per "rel"
                // we need to transform the "rel" element into
                // an array, so that we can add oher "rel"
                // elements
                if (isset($links[$link->getRel()]['href'])) {
                    $element = $links[$link->getRel()];

                    $links[$link->getRel()] = array();
                    $links[$link->getRel()][] = $element;
                }

                $links[$link->getRel()][] = $data;
            } else {
                $links[$link->getRel()] = $data;
            }
        }

        return $links;
    }
}
