<?php


namespace Tagcade\Model\Core;


use Doctrine\Common\Collections\ArrayCollection;

class VideoWaterfallTagItem implements VideoWaterfallTagItemInterface
{
    const STRATEGY_LINEAR = 'linear';
    const STRATEGY_PARALLELS = 'parallel';

    static $STRATEGY_DEFAULT = self::STRATEGY_LINEAR;

    protected $id;
    protected $position;
    protected $strategy;

    /** @var VideoWaterfallTagInterface */
    protected $videoWaterfallTag;

    /** @var VideoDemandAdTagInterface[] */
    protected $videoDemandAdTags;

    protected $deletedAt;

    function __construct()
    {
        $this->strategy = self::$STRATEGY_DEFAULT;
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
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @inheritdoc
     */
    public function setPosition($position)
    {
        $this->position = $position;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getStrategy()
    {
        return $this->strategy;
    }

    /**
     * @inheritdoc
     */
    public function setStrategy($strategy)
    {
        $this->strategy = $strategy;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getVideoWaterfallTag()
    {
        return $this->videoWaterfallTag;
    }

    /**
     * @inheritdoc
     */
    public function setVideoWaterfallTag(VideoWaterfallTagInterface $videoWaterfallTag)
    {
        $this->videoWaterfallTag = $videoWaterfallTag;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getVideoDemandAdTags()
    {
        return $this->videoDemandAdTags;
    }

    /**
     * @inheritdoc
     */
    public function addVideoDemandAdTag(VideoDemandAdTagInterface $demandAdTag)
    {
        if ($this->videoDemandAdTags === null) {
            $this->videoDemandAdTags = new ArrayCollection();
        }

        $this->videoDemandAdTags[] = $demandAdTag;

        return $this;
    }


    /**
     * @inheritdoc
     */
    public function setVideoDemandAdTags($videoDemandAdTags)
    {
        $this->videoDemandAdTags = $videoDemandAdTags;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * @inheritdoc
     */
    public function setDeletedAt($date)
    {
        $this->deletedAt = $date;
        return $this;
    }
}