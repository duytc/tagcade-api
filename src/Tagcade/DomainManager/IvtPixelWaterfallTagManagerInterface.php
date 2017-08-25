<?php

namespace Tagcade\DomainManager;

use Tagcade\Model\Core\IvtPixelInterface;
use Tagcade\Model\Core\IvtPixelWaterfallTagInterface;
use Tagcade\Model\Core\VideoWaterfallTagInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface IvtPixelWaterfallTagManagerInterface extends ManagerInterface
{
    /**
     * get all IvtPixelWaterfallTags For a Publisher
     *
     * @param PublisherInterface $publisher
     * @param null $limit
     * @param null $offset
     * @return array|IvtPixelWaterfallTagInterface[]
     */
    public function getIvtPixelWaterfallTagsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);

    /**
     * get all IvtPixelWaterfallTags according IvtPixel
     * @param IvtPixelInterface $ivtPixel
     * @param null $limit
     * @param null $offset
     * @return mixed
     */
    public function getIvtPixelWaterfallTagsByIvtPixel(IvtPixelInterface $ivtPixel, $limit = null, $offset = null);

    /**
     * get all IvtPixelWaterfallTags according VideoWaterfallTag
     *
     * @param VideoWaterfallTagInterface $videoWaterfallTag
     * @param null $limit
     * @param null $offset
     * @return array|IvtPixelWaterfallTagInterface[]
     */
    public function getIvtPixelWaterfallTagsByWaterfallTag(VideoWaterfallTagInterface $videoWaterfallTag, $limit = null, $offset = null);
}