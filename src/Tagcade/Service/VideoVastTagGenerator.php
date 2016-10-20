<?php

namespace Tagcade\Service;

use Doctrine\Common\Collections\Collection;
use Tagcade\DomainManager\VideoDemandAdTagManagerInterface;
use Tagcade\DomainManager\VideoPublisherManagerInterface;
use Tagcade\DomainManager\VideoWaterfallTagManagerInterface;
use Tagcade\Model\Core\VideoDemandAdTagInterface;
use Tagcade\Model\Core\VideoPublisherInterface;
use Tagcade\Model\Core\VideoTargetingInterface;
use Tagcade\Model\Core\VideoWaterfallTagInterface;

class VideoVastTagGenerator
{
    protected static $SUPPORTED_MACROS_IN_VAST_TAG_URL = [
        //VideoTargetingInterface::TARGETING_REQUIRED_MACRO_IP_ADDRESS, // not allowed to be set via url parameter
        //VideoTargetingInterface::TARGETING_REQUIRED_MACRO_USER_AGENT, //not allowed to be set via url parameter
        VideoTargetingInterface::TARGETING_REQUIRED_MACRO_PAGE_URL,
        VideoTargetingInterface::TARGETING_REQUIRED_MACRO_DOMAIN,
        VideoTargetingInterface::TARGETING_REQUIRED_MACRO_PAGE_TITLE,
        VideoTargetingInterface::TARGETING_REQUIRED_MACRO_PLAYER_WIDTH,
        VideoTargetingInterface::TARGETING_REQUIRED_MACRO_PLAYER_HEIGHT,
        VideoTargetingInterface::TARGETING_REQUIRED_MACRO_PLAYER_DIMENSIONS,
        VideoTargetingInterface::TARGETING_REQUIRED_MACRO_PLAYER_SIZE,
        VideoTargetingInterface::TARGETING_REQUIRED_MACRO_VIDEO_DURATION,
        VideoTargetingInterface::TARGETING_REQUIRED_MACRO_VIDEO_URL,
        VideoTargetingInterface::TARGETING_REQUIRED_MACRO_VIDEO_ID,
        VideoTargetingInterface::TARGETING_REQUIRED_MACRO_VIDEO_TITLE,
        VideoTargetingInterface::TARGETING_REQUIRED_MACRO_VIDEO_DESCRIPTION,
        VideoTargetingInterface::TARGETING_REQUIRED_MACRO_APP_NAME,
        VideoTargetingInterface::TARGETING_REQUIRED_MACRO_USER_LAT,
        VideoTargetingInterface::TARGETING_REQUIRED_MACRO_USER_LON
    ];

    protected $videoVastTagBaseUrl;

    /** @var VideoWaterfallTagManagerInterface */
    private $videoWaterfallTagManager;

    /** @var VideoPublisherManagerInterface */
    private $videoPublisherManager;

    /** @var VideoDemandAdTagManagerInterface */
    private $videoDemandAdTagManager;

    /**
     * @param array $videoVastTagBaseUrl
     * @param VideoWaterfallTagManagerInterface $videoWaterfallTagManager
     * @param VideoPublisherManagerInterface $videoPublisherManager
     * @param VideoDemandAdTagManagerInterface $videoDemandAdTagManager
     */
    public function __construct($videoVastTagBaseUrl,
                                VideoWaterfallTagManagerInterface $videoWaterfallTagManager,
                                VideoPublisherManagerInterface $videoPublisherManager,
                                VideoDemandAdTagManagerInterface $videoDemandAdTagManager
    )
    {
        $this->videoVastTagBaseUrl = $videoVastTagBaseUrl;
        $this->videoWaterfallTagManager = $videoWaterfallTagManager;
        $this->videoPublisherManager = $videoPublisherManager;
        $this->videoDemandAdTagManager = $videoDemandAdTagManager;
    }

    /**
     * get all Tags For Site
     *
     * @param VideoPublisherInterface $videoPublisher
     * @param bool $isSecure true => use https, false => use http
     * @return array
     */
    public function getVideoVastTagsForVideoPublisher(VideoPublisherInterface $videoPublisher, $isSecure)
    {
        /** @var Collection|VideoWaterfallTagInterface[] $allVideoWaterfallTags */
        $allVideoWaterfallTags = $this->videoWaterfallTagManager->getVideoWaterfallTagsForVideoPublisher($videoPublisher);

        if ($allVideoWaterfallTags instanceof Collection) {
            $allVideoWaterfallTags = $allVideoWaterfallTags->toArray();
        }

        return $this->getVideoVastTagsForVideoWaterfallTag($allVideoWaterfallTags, $isSecure);
    }

    /**
     * get Tags For AdSlots
     *
     * @param VideoWaterfallTagInterface[] $videoWaterfallTags
     * @param bool $isSecure true => use https, false => use http
     * @return array format as:
     * [
     *      videoWaterfallTagId => [
     *          vasttag => <video vast tags>, // url to get vast document, identified by waterfallTag's uuid
     *          name => <name of waterfallTag>
     *      ],
     *      ...
     * ],
     * return empty if no video vast tags created
     */
    private function getVideoVastTagsForVideoWaterfallTag(array $videoWaterfallTags, $isSecure)
    {
        // filter all videoWaterfallTags have publisher that has video module enabled
        $filteredVideoWaterfallTags = array_filter($videoWaterfallTags, function ($videoWaterfallTag) {
            return
                $videoWaterfallTag instanceof VideoWaterfallTagInterface;
            // todo: also check if has video module. Currently confused module video and video_analytics, checking is incorrect!
            //$videoWaterfallTag instanceof VideoWaterfallTagInterface
            //&& $videoWaterfallTag->getVideoPublisher()->getPublisher()->hasVideoModule();
        });

        if (count($filteredVideoWaterfallTags) < 1) {
            return [];
        }

        // create video vast tags
        $videoVastTags = [];

        /** @var VideoWaterfallTagInterface $videoWaterfallTag */
        foreach ($filteredVideoWaterfallTags as $videoWaterfallTag) {
            if (array_key_exists($videoWaterfallTag->getId(), $videoVastTags)) {
                continue; // skip generating for existed id
            }

            $videoVastTags[$videoWaterfallTag->getId()] = array(
                'vasttag' => $this->createVideoVastTags($videoWaterfallTag, $isSecure),
                'name' => $videoWaterfallTag->getName()
            );
        }

        return $videoVastTags;
    }

    /**
     * create Video Vast Tags
     * example:
     * https://vast-tag-server.tagcade.com/tag.php?id=1e5cd616-0860-492d-9509-58f4c0ee8ce3&page_url=google.com&player_width=480
     * where:
     * -  https://vast-tag-server.tagcade.com/tag.php?id=UUID: vast_tag_base_url in parameter.yml config, using secure = true
     * - 1e5cd616-0860-492d-9509-58f4c0ee8ce3: UUID of video waterfall tag, replaced for UUID pattern
     * - &page_url=google.com&player_width=480: query string built from all required macros of all video demand ad tags belong to the video waterfall tag
     *
     * @param VideoWaterfallTagInterface $videoWaterfallTag
     * @param bool $isSecure true => use https, false => use http
     * @return string
     */
    public function createVideoVastTags(VideoWaterfallTagInterface $videoWaterfallTag, $isSecure)
    {
        /* template: <$base_url> contain pattern UUID for replacing */
        $vastTagUrlTemplate = $this->videoVastTagBaseUrl;

        // process secure
        $vastTagUrlTemplate = $isSecure
            ? preg_replace("/^http:/i", "https:", $vastTagUrlTemplate)
            : preg_replace("/^https:/i", "http:", $vastTagUrlTemplate);

        $vastTagUrl = str_replace('UUID', $videoWaterfallTag->getUuid(), $vastTagUrlTemplate);

        $queryString = $this->createQueryStringForRequiredMacros($videoWaterfallTag);

        if (empty($queryString)) {
            return $vastTagUrl;
        }

        if (strpos($vastTagUrl, '?') === false) {
            $vastTagUrl .= '?';
        } else {
            $vastTagUrl .= '&';
        }

        $vastTagUrl .= $queryString;

        return $vastTagUrl;
    }

    /**
     * create Query string for all required macros of all video demand ad tags belong to the video waterfall tag
     *
     * @param VideoWaterfallTagInterface $videoWaterfallTag
     * @return string
     */
    private function createQueryStringForRequiredMacros(VideoWaterfallTagInterface $videoWaterfallTag)
    {
        // get all required macros
        /** @var VideoDemandAdTagInterface[] $videoDemandAdTags */
        $videoDemandAdTags = $this->videoDemandAdTagManager->getVideoDemandAdTagsForVideoWaterfallTag($videoWaterfallTag);

        $allRequiredMacros = [];
        foreach ($videoDemandAdTags as $videoDemandAdTag) {
            $targeting = $videoDemandAdTag->getTargeting();
            if (!is_array($targeting) || !array_key_exists(VideoTargetingInterface::TARGETING_KEY_REQUIRED_MACROS, $targeting)) {
                continue;
            }

            $requiredMacros = $targeting[VideoTargetingInterface::TARGETING_KEY_REQUIRED_MACROS];
            if (!is_array($requiredMacros)) {
                continue;
            }

            $allRequiredMacros = array_merge($allRequiredMacros, $requiredMacros);
        }

        // sure unique required macros
        $allRequiredMacros = array_unique($allRequiredMacros);

        // create query string
        $queryString = '';

        if (empty($allRequiredMacros)) {
            return $queryString;
        }

        foreach ($allRequiredMacros as $macro) {
            $queryString .= sprintf('&%s=[REPLACE_ME]', $macro);
        }

        // remove first &
        $queryString = substr($queryString, 1);

        return $queryString;
    }
}