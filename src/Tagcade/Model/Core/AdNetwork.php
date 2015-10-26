<?php

namespace Tagcade\Model\Core;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
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
    protected $activeAdTagsCount;
    protected $pausedAdTagsCount;

    public function __construct()
    {
        // we use reflection class to create new object, there's no need to initialize $activeAdTagsCount
        // and $pausedAdTagsCount here
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

    /**
     * @inheritdoc
     */
    public function getActiveAdTagsCount()
    {
        return $this->activeAdTagsCount;
    }

    /**
     * @inheritdoc
     */
    public function setActiveAdTagsCount($activeAdTagsCount)
    {
        $this->activeAdTagsCount = $activeAdTagsCount;

        return $this;
    }

    /**
     * @return self
     */
    public function increaseActiveAdTagsCount()
    {
        if ($this->activeAdTagsCount === null || $this->activeAdTagsCount == 0) {
            $this->activeAdTagsCount = 1;
        }
        else ++$this->activeAdTagsCount;

        return $this;
    }

    /**
     * @return self
     */
    public function decreaseActiveAdTagsCount()
    {
        if ($this->activeAdTagsCount === null || $this->activeAdTagsCount < 2) {
            $this->activeAdTagsCount = 0;
        }
        else --$this->activeAdTagsCount;

        return $this;
    }


    /**
     * @inheritdoc
     */
    public function getPausedAdTagsCount()
    {
        return $this->pausedAdTagsCount;
    }

    /**
     * @inheritdoc
     */
    public function setPausedAdTagsCount($pausedAdTagsCount)
    {
        $this->pausedAdTagsCount = $pausedAdTagsCount;

        return $this;
    }

    /**
     * @return self
     */
    public function increasePausedAdTagsCount()
    {
        if ($this->pausedAdTagsCount === null || $this->pausedAdTagsCount == 0) {
            $this->pausedAdTagsCount = 1;
        }
        else ++$this->pausedAdTagsCount;

        return $this;
    }

    /**
     * @return self
     */
    public function decreasePausedAdTagsCount()
    {
        if ($this->pausedAdTagsCount === null || $this->pausedAdTagsCount < 2) {
            $this->pausedAdTagsCount = 0;
        }
        else --$this->pausedAdTagsCount;

        return $this;
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