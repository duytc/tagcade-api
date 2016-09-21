<?php


namespace Tagcade\Model\Core;


use Tagcade\Model\ModelInterface;

interface VideoWaterfallTagItemInterface extends ModelInterface
{
    /**
     * @return mixed
     */
    public function getPosition();

    /**
     * @param mixed $position
     * @return self
     */
    public function setPosition($position);

    /**
     * @return mixed
     */
    public function getStrategy();

    /**
     * @param mixed $strategy
     * @return self
     */
    public function setStrategy($strategy);

    /**
     * @return VideoWaterfallTagInterface
     */
    public function getVideoWaterfallTag();

    /**
     * @param VideoWaterfallTagInterface $videoWaterfallTag
     * @return self
     */
    public function setVideoWaterfallTag(VideoWaterfallTagInterface $videoWaterfallTag);

    /**
     * @return array|VideoDemandAdTagInterface[]
     */
    public function getVideoDemandAdTags();

    /**
     * @param VideoDemandAdTagInterface $demandAdTag
     * @return $this
     */
    public function addVideoDemandAdTag(VideoDemandAdTagInterface $demandAdTag);

    /**
     * @param array|VideoDemandAdTagInterface[] $videoDemandAdTags
     */
    public function setVideoDemandAdTags($videoDemandAdTags);

    /**
     * @return mixed
     */
    public function getDeletedAt();

    /**
     * @param $date
     * @return mixed
     */
    public function setDeletedAt($date);
}