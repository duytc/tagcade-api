<?php

namespace Tagcade\Model\Core;

use Doctrine\Common\Collections\ArrayCollection;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\UserEntityInterface;

class AdNetwork implements AdNetworkInterface
{
    protected $id;

    /**
     * @var UserEntityInterface
     */
    protected $publisher;
    protected $name;
    protected $url;
    protected $active;
    protected $adTags;

    /**
     * This is the default CPM assigned to all ad tags unless it is overwritten
     */
    protected $defaultCpmRate;

    public function __construct()
    {
        $this->adTags = new ArrayCollection();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getPublisher()
    {
        return $this->publisher;
    }

    /**
     * @inheritdoc
     */
    public function getPublisherId()
    {
        if (!$this->publisher) {
            return null;
        }

        return $this->publisher->getId();
    }

    /**
     * @inheritdoc
     */
    public function setPublisher(PublisherInterface $publisher) {
        $this->publisher = $publisher->getUser();
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @inheritdoc
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @inheritdoc
     */
    public function setActive($boolean)
    {
        $this->active = (Boolean) $boolean;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getDefaultCpmRate()
    {
        return $this->defaultCpmRate;
    }

    /**
     * @inheritdoc
     */
    public function setDefaultCpmRate($defaultCpmRate)
    {
        $this->defaultCpmRate = $defaultCpmRate;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAdTags()
    {
        return $this->adTags;
    }

    public function __toString()
    {
        return $this->name;
    }
}