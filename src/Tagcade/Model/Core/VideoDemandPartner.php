<?php

namespace Tagcade\Model\Core;


use Tagcade\Model\User\UserEntityInterface;
use Tagcade\Service\StringUtilTrait;

class VideoDemandPartner implements VideoDemandPartnerInterface
{
    use StringUtilTrait;
    protected $id;

    protected $name;
    protected $nameCanonical;
    protected $defaultTagURL;
    protected $activeAdTagsCount;
    protected $pausedAdTagsCount;
    protected $libraryVideoDemandAdTags;


    /** @var UserEntityInterface $publisher */
    protected $publisher;

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
        $this->nameCanonical = $this->normalizeName($name);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getNameCanonical()
    {
        return $this->nameCanonical;
    }

    /**
     * @inheritdoc
     */
    public function getDefaultTagURL()
    {
        return $this->defaultTagURL;
    }

    /**
     * @return mixed
     */
    public function getActiveAdTagsCount()
    {
        return $this->activeAdTagsCount;
    }

    /**
     * @param mixed $activeAdTagsCount
     * @return self
     */
    public function setActiveAdTagsCount($activeAdTagsCount)
    {
        $this->activeAdTagsCount = $activeAdTagsCount;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPausedAdTagsCount()
    {
        return $this->pausedAdTagsCount;
    }

    /**
     * @param mixed $pausedAdTagsCount
     * @return self
     */
    public function setPausedAdTagsCount($pausedAdTagsCount)
    {
        $this->pausedAdTagsCount = $pausedAdTagsCount;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setDefaultTagURL($defaultTagURL)
    {
        $this->defaultTagURL = $defaultTagURL;
        return $this;
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
    public function setPublisher(UserEntityInterface $publisher)
    {
        $this->publisher = $publisher;
        return $this;
    }

    public function increasePausedAdTagsCount()
    {
        if ($this->pausedAdTagsCount === null || $this->pausedAdTagsCount == 0) {
            $this->pausedAdTagsCount = 1;
        } else ++$this->pausedAdTagsCount;

        return $this;
    }

    public function decreasePausedAdTagsCount()
    {
        if ($this->pausedAdTagsCount === null || $this->pausedAdTagsCount < 2) {
            $this->pausedAdTagsCount = 0;
        } else --$this->pausedAdTagsCount;

        return $this;
    }

    public function increaseActiveAdTagsCount()
    {
        if ($this->activeAdTagsCount === null || $this->activeAdTagsCount == 0) {
            $this->activeAdTagsCount = 1;
        } else ++$this->activeAdTagsCount;

        return $this;
    }

    public function decreaseActiveAdTagsCount()
    {
        if ($this->activeAdTagsCount === null || $this->activeAdTagsCount < 2) {
            $this->activeAdTagsCount = 0;
        } else --$this->activeAdTagsCount;

        return $this;
    }

    public function getVideoDemandAdTags()
    {
        $allAdTags = [];
        $tagLibs = $this->libraryVideoDemandAdTags->toArray();

        array_walk(
            $tagLibs,
            function (LibraryVideoDemandAdTagInterface $libraryVideoDemandAdTag) use (&$allAdTags) {
                $adTags = $libraryVideoDemandAdTag->getVideoDemandAdTags()->toArray();
                $allAdTags = array_merge($allAdTags, $adTags);
            }
        );

        return array_unique($allAdTags);
    }

    /**
     * @return mixed
     */
    public function getLibraryVideoDemandAdTags()
    {
        return $this->libraryVideoDemandAdTags;
    }
}