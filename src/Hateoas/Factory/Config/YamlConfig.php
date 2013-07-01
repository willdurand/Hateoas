<?php

namespace Hateoas\Factory\Config;

use Symfony\Component\Yaml\Yaml;

/**
 * @author William Durand <william.durand1@gmail.com>
 */
class YamlConfig implements ConfigInterface
{
    private $config = array();

    public function __construct($file, $cacheDir = null)
    {
        if (!file_exists($file)) {
            throw new \InvalidArgumentException(sprintf('The file "%s" does not exist', $file));
        }

        if ($cacheDir) {
            if (!is_dir($cacheDir) && ! @mkdir($cacheDir, 0777, true)) {
                throw new \InvalidArgumentException(sprintf('The directory "%s" does not exist and could not be created.', $cacheDir));
            }

            if (!is_writable($cacheDir)) {
                throw new \InvalidArgumentException(sprintf('The directory "%s" is not writable.', $cacheDir));
            }

            $cacheFile = sprintf('%s/%s.yml.cache', $cacheDir, md5($file));

            if (file_exists($cacheFile)) {
                $config = json_decode(file_get_contents($cacheFile), true);

                if (isset($config['hateoas'])) {
                    $this->config = $config['hateoas'];
                }

                return;
            }
        }

        $config = Yaml::parse(file_get_contents($file));

        if ($cacheDir) {
            file_put_contents($cacheFile, json_encode($config));
        }

        if (isset($config['hateoas'])) {
            $this->config = $config['hateoas'];
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getResourceDefinitions()
    {
        return isset($this->config['resources']) ? $this->config['resources'] : array();
    }

    /**
     * {@inheritDoc}
     */
    public function getCollectionDefinitions()
    {
        return isset($this->config['collections']) ? $this->config['collections'] : array();
    }
}
