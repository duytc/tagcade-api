<?php


namespace Tagcade\Model\Core;


use Doctrine\Common\Collections\Collection;
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
     * @return array|VideoDemandAdTagInterface[]|Collection
     */
    public function getVideoDemandAdTags();

    /**
     * @param VideoDemandAdTagInterface $demandAdTag
     * @return $this
     */
    public function addVideoDemandAdTag(VideoDemandAdTagInterface $demandAdTag);

    /**
     * @param array|VideoDemandAdTagInterface[]||Collection $videoDemandAdTags
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