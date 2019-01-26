<?php

declare(strict_types=1);

namespace Hateoas\Serializer;

use Hateoas\Model\Embedded;
use Hateoas\Model\Link;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Visitor\SerializationVisitorInterface;

interface SerializerInterface
{
    /**
     * @param Link[]                   $links
     */
    public function serializeLinks(array $links, SerializationVisitorInterface $visitor, SerializationContext $context): void;

    /**
     * @param Embedded[]               $embeddeds
     */
    public function serializeEmbeddeds(array $embeddeds, SerializationVisitorInterface $visitor, SerializationContext $context): void;
}
