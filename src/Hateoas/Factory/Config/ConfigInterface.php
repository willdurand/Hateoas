<?php

namespace Hateoas\Factory\Config;

/**
 * @author William Durand <william.durand1@gmail.com>
 */
interface ConfigInterface
{
    /**
     * @return array
     */
    public function getResourceDefinitions();

    /**
     * @return array
     */
    public function getCollectionDefinitions();
}
