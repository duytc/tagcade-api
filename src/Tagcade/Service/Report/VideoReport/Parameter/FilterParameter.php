<?php
namespace Tagcade\Service\Report\VideoReport\Parameter;

use DateTime;

class FilterParameter implements FilterParameterInterface
{
    const START_DATE_KEY = 'startDate';
    const END_DATE_KEY = 'endDate';
    const PUBLISHER_KEY = 'publisher';
    const VIDEO_PUBLISHER_KEY = 'videoPublisher';
    const VIDEO_DEMAND_PARTNER_KEY = 'demandPartner';
    const VIDEO_WATERFALL_TAG_KEY = 'waterfallTag';
    const VIDEO_DEMAND_AD_TAG_KEY = 'videoDemandAdTag';

    const START_DATE_DEFAULT_VALUE = null;
    const END_DATE_DEFAULT_VALUE = null;
    const PUBLISHER_DEFAULT_VALUE = null;
    const VIDEO_DEMAND_PARTNER_DEFAULT_VALUE = null;
    const VIDEO_WATERFALL_TAG_DEFAULT_VALUE = null;
    const VIDEO_DEMAND_AD_TAG_DEFAULT_VALUE = null;

    protected $startDate;
    protected $endDate;
    protected $publishers;
    protected $videoPublishers;
    protected $videoDemandPartners;
    protected $videoWaterfallTags;
    protected $videoDemandAdTags;

    function __construct(array $filterElements)
    {
        if (array_key_exists(self::PUBLISHER_KEY, $filterElements) && is_array($filterElements[self::PUBLISHER_KEY])) {
            $this->publishers = $filterElements[self::PUBLISHER_KEY];
        }

        if (array_key_exists(self::VIDEO_PUBLISHER_KEY, $filterElements) && is_array($filterElements[self::VIDEO_PUBLISHER_KEY])) {
            $this->videoPublishers = $filterElements[self::VIDEO_PUBLISHER_KEY];
        }

        if (array_key_exists(self::VIDEO_DEMAND_PARTNER_KEY, $filterElements) && is_array($filterElements[self::VIDEO_DEMAND_PARTNER_KEY])) {
            $this->videoDemandPartners = $filterElements[self::VIDEO_DEMAND_PARTNER_KEY];
        }

        if (array_key_exists(self::VIDEO_WATERFALL_TAG_KEY, $filterElements) && is_array($filterElements[self::VIDEO_WATERFALL_TAG_KEY])) {
            $this->videoWaterfallTags = $filterElements[self::VIDEO_WATERFALL_TAG_KEY];
        }

        if (array_key_exists(self::VIDEO_DEMAND_AD_TAG_KEY, $filterElements) && is_array($filterElements[self::VIDEO_DEMAND_AD_TAG_KEY])) {
            $this->videoDemandAdTags = $filterElements[self::VIDEO_DEMAND_AD_TAG_KEY];
        }

        if (array_key_exists(self::START_DATE_KEY, $filterElements)) {
            $this->startDate = new DateTime($filterElements[self::START_DATE_KEY]);
        }

        if (array_key_exists(self::END_DATE_KEY, $filterElements)) {
            $this->endDate = new DateTime($filterElements[self::END_DATE_KEY]);
        }
    }

    /**
     * @return null|DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @return array
     */
    public function getPublishers()
    {
        return $this->publishers;
    }

    /**
     * @return null|DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @return array
     */
    public function getVideoDemandAdTags()
    {
        return $this->videoDemandAdTags;
    }

    /**
     * @return array
     */
    public function getVideoWaterfallTags()
    {
        return $this->videoWaterfallTags;
    }

    /**
     * @return array
     */
    public function getVideoDemandPartners()
    {
        return $this->videoDemandPartners;
    }

    /**
     * @param array $publisherIds
     */
    public function setPublisherId(array $publisherIds)
    {
        $this->publishers = $publisherIds;
    }

    /**
     * @return array
     */
    public function getVideoPublishers()
    {
        return $this->videoPublishers;
    }
}