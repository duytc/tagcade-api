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
    protected $libraryAdTags;
    /**
     * This is the default CPM assigned to all ad tags unless it is overwritten
     */
    protected $defaultCpmRate;

    public function __construct()
    {
        $this->libraryAdTags = new ArrayCollection();
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

    public function getActiveAdTagsCount()
    {
        $allTags = $this->getAdTags();

        return count(array_filter($allTags, function (AdTagInterface $adTag) { return $adTag->isActive() === true; }));
    }

    public function getPausedAdTagsCount()
    {
        $allTags = $this->getAdTags();

        return count(array_filter($allTags, function (AdTagInterface $adTag) { return $adTag->isActive() === false; }));
    }

    /**
     * @inheritdoc
     */
    public function getAdTags()
    {
        $allAdTags = [];
        $tagLibs = $this->libraryAdTags->toArray();
        array_walk(
            $tagLibs,
            function(LibraryAdTagInterface $libraryAdTag) use(&$allAdTags){
                $adTags = $libraryAdTag->getAdTags()->toArray();
                $allAdTags = array_merge($allAdTags, $adTags);
            }
        );

        return array_unique($allAdTags);
    }

    public function __toString()
    {
        return $this->name;
    }
}