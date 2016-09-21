<?php


namespace Tagcade\Repository\Core;


use Doctrine\Common\Persistence\ObjectRepository;
use Tagcade\Model\Core\VideoWaterfallTag;
use Tagcade\Model\Core\VideoWaterfallTagInterface;
use Tagcade\Model\Core\VideoWaterfallTagItemInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface VideoWaterfallTagItemRepositoryInterface extends ObjectRepository
{
    /**
     * get all VideoWaterfallTagItems For a Publisher
     *
     * @param PublisherInterface $publisher
     * @param null $limit
     * @param null $offset
     * @return array|VideoWaterfallTagItemInterface[]
     */
    public function getVideoWaterfallTagItemsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);

    /**
     * @param VideoWaterfallTagInterface $videoWaterfallTag
     * @param null $limit
     * @param null $offset
     * @return mixed
     */
    public function getVideoWaterfallTagItemsForAdTag(VideoWaterfallTagInterface $videoWaterfallTag, $limit = null, $offset = null);

    /**
     * @param VideoWaterfallTagInterface $waterfallTag
     * @return mixed
     */
    public function getMaxPositionInWaterfallTag(VideoWaterfallTagInterface $waterfallTag);

    /**
     * @param VideoWaterfallTagInterface $videoWaterfallTag
     * @param $position
     * @return null|VideoWaterfallTagItemInterface
     */
    public function getWaterfallTagItemWithPositionInWaterfallTag(VideoWaterfallTagInterface $videoWaterfallTag, $position);
}