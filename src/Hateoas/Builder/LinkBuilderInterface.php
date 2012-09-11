<?php

namespace Hateoas\Builder;

use Hateoas\Factory\Definition\LinkDefinition;

/**
 * @author William Durand <william.durand1@gmail.com>
 */
interface LinkBuilderInterface
{
    /**
     * @param  LinkDefinition $definition
     * @param  object         $data
     * @return Link
     */
    public function createFromDefinition(LinkDefinition $definition, $data);
}
