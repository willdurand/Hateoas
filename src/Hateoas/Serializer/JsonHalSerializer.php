<?php

namespace Hateoas\Serializer;

use JMS\Serializer\JsonSerializationVisitor;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class JsonHalSerializer implements JsonSerializerInterface
{
    /**
     * {@inheritdoc}
     */
    public function serializeLinks(array $links, JsonSerializationVisitor $visitor)
    {
        $serializedLinks = array();

        foreach ($links as $link) {
            $serializedLink = array(
                'href' => $link->getHref(),
            );
            $serializedLink = array_merge($serializedLink, $link->getAttributes());

            if (!isset($serializedLinks[$link->getRel()])) {
                $serializedLinks[$link->getRel()] = $serializedLink;
            } else if (isset($serializedLinks[$link->getRel()]['href'])) {
                $serializedLinks[$link->getRel()] = array(
                    $serializedLinks[$link->getRel()],
                    $serializedLink
                );
            } else {
                $serializedLinks[$link->getRel()][] = $serializedLink;
            }
        }

        $visitor->addData('_links', $serializedLinks);
    }
}
