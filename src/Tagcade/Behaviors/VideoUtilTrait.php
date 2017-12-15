<?php


namespace Tagcade\Behaviors;


use Tagcade\Model\Core\LibraryVideoDemandAdTagInterface;
use Tagcade\Model\Core\VideoDemandAdTagInterface;
use Tagcade\Model\Core\VideoTargetingInterface;
use Tagcade\Model\Core\VideoWaterfallTagInterface;
use Tagcade\Service\StringUtilTrait;

trait VideoUtilTrait
{
    use StringUtilTrait;
    
    /**
     * @param VideoWaterfallTagInterface $videoWaterfallTag
     * @return VideoWaterfallTagInterface
     */
    public function addDefaultValueForVideoWaterfallTag(VideoWaterfallTagInterface $videoWaterfallTag)
    {
        if (empty($videoWaterfallTag->getUuid())) {
            $uuid = $this->generateUuidV4();
            $videoWaterfallTag->setUuid($uuid);
        }

        if (empty($videoWaterfallTag->getVideoWaterfallTagItems())) {
            $videoWaterfallTag->setVideoWaterfallTagItems([]);
        }

        if (empty($videoWaterfallTag->getTargeting())) {
            $videoWaterfallTag->setTargeting([]);
        }

        if (empty($videoWaterfallTag->getPlatform())) {
            $videoWaterfallTag->setPlatform(['flash']);
        }

        if (empty($videoWaterfallTag->getRunOn())) {
            $videoWaterfallTag->setRunOn('Server-Side VAST+VAPID');
        }

        return $videoWaterfallTag;
    }

    /**
     * @param LibraryVideoDemandAdTagInterface $libraryVideoDemandAdTag
     * @return LibraryVideoDemandAdTagInterface
     */
    public function addDefaultValueForLibraryVideoDemandAdTag(LibraryVideoDemandAdTagInterface $libraryVideoDemandAdTag)
    {
        if (empty($libraryVideoDemandAdTag->getTargeting())) {
            $libraryVideoDemandAdTag->setTargeting([
                VideoTargetingInterface::TARGETING_KEY_COUNTRIES => [],
                VideoTargetingInterface::TARGETING_KEY_DOMAINS => [],
                VideoTargetingInterface::TARGETING_KEY_EXCLUDE_COUNTRIES => [],
                VideoTargetingInterface::TARGETING_KEY_EXCLUDE_DOMAINS => [],
                VideoTargetingInterface::TARGETING_KEY_PLATFORM => [],
                VideoTargetingInterface::TARGETING_KEY_PLAYER_SIZE => [],
                VideoTargetingInterface::TARGETING_KEY_REQUIRED_MACROS => []]);
        }

        return $libraryVideoDemandAdTag;
    }

    /**
     * @param VideoDemandAdTagInterface $videoDemandAdTag
     * @return VideoDemandAdTagInterface
     */
    public function addDefaultValuesForVideoDemandAdTag(VideoDemandAdTagInterface $videoDemandAdTag) {
        $videoDemandAdTag->setTargetingOverride(false);

        return $videoDemandAdTag;
    }

    /**
     * @param VideoDemandAdTagInterface $videoDemandAdTag
     * @return VideoDemandAdTagInterface
     */
    public function addDefaultValueForVideoDemandAdTag(VideoDemandAdTagInterface $videoDemandAdTag) {
        $videoDemandAdTag->setTargetingOverride(false);

        return $videoDemandAdTag;
    }
}