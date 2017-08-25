<?php

namespace Tagcade\Model\Core;

use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\UserEntityInterface;

class IvtPixel implements IvtPixelInterface
{
    static $FIRE_ON_DEFAULT = self::FIRE_ON_REQUEST;
    const LIMIT = 100;
    const FIRE_ON_REQUEST = 'request';
    const FIRE_ON_IMPRESSION = 'impression';

    protected $id;
    protected $name;
    protected $urls;
    protected $fireOn;
    protected $runningLimit;

    /** @var PublisherInterface */
    protected $publisher;

    /** @var IvtPixelWaterfallTagInterface[] */
    protected $ivtPixelWaterfallTags;

    public function __construct()
    {
        $this->fireOn = self::$FIRE_ON_DEFAULT;
        $this->runningLimit = self::LIMIT;
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
    public function setPublisher(PublisherInterface $publisher)
    {
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
    public function getUrls()
    {
        return $this->urls;
    }

    /**
     * @inheritdoc
     */
    public function setUrls($urls)
    {
        $this->urls = $urls;

        return $this;
    }

    /**
     * @return int
     */
    public function getRunningLimit()
    {
        return $this->runningLimit;
    }

    /**
     * @inheritdoc
     */
    public function setRunningLimit($runningLimit)
    {
        $this->runningLimit = $runningLimit;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getFireOn()
    {
        return $this->fireOn;
    }

    /**
     * @inheritdoc
     */
    public function setFireOn($fireOn)
    {
        $this->fireOn = $fireOn;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getIvtPixelWaterfallTags()
    {
        return $this->ivtPixelWaterfallTags;
    }

    /**
     * @inheritdoc
     */
    public function setIvtPixelWaterfallTags($ivtPixelWaterfallTags)
    {
        $this->ivtPixelWaterfallTags = $ivtPixelWaterfallTags;

        return $this;
    }
}