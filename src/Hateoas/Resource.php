<?php

namespace Hateoas;

use JMS\Serializer\Annotation\SerializedName;

/**
 * @author William Durand <william.durand1@gmail.com>
 */
class Resource
{
    /**
     * @var object|array
     */
    private $data;

    /**
     * @var array
     */
    private $forms;

    /**
     * @SerializedName("_links")
     * @var array
     */
    private $links;

    /**
     * @SerializedName("_embeds")
     * @var array
     */
    private $embeds;

    public function __construct($data, $links = array(), array $forms = array(), array $embeds = array())
    {
        $this->data = $data;
        $this->links = $links;
        $this->forms = $forms;
        $this->embeds = $embeds;
    }

    /**
     * @return object|array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function getLinks()
    {
        return $this->links;
    }

    /**
     * @return array
     */
    public function getEmbeds()
    {
        return $this->embeds;
    }

    /**
     * @param  Link     $link
     * @return Resource
     */
    public function addLink(Link $link)
    {
        if (!in_array($link, $this->links)) {
            $this->links[] = $link;
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getForms()
    {
        return $this->forms;
    }

    /**
     * @param array $forms
     */
    public function setForms(array $forms)
    {
        $this->forms = $forms;

        return $this;
    }
}
