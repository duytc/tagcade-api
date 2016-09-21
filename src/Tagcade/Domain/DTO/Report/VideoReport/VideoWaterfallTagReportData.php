<?php


namespace Tagcade\Domain\DTO\Report\VideoReport;


class VideoWaterfallTagReportData extends VideoAbstractReportData implements VideoWaterfallTagReportDataInterface
{
    protected $videoWaterfallTagId;

    /**
     * VideoWaterfallTagReportData constructor.
     * @param $videoWaterfallTagId
     * @param array $redisReportData
     * @param $day = null
     */
    public function __construct($videoWaterfallTagId, array $redisReportData, $day = null)
    {
        $reportDay = $day instanceof \DateTime ? $day : new \DateTime('today');

        $cacheKeyRequests = $this->getCacheKey(self::CACHE_KEY_REQUESTS, sprintf(self::NAMESPACE_AD_TAG, $videoWaterfallTagId), $reportDay);
        $cacheKeyImpressions = $this->getCacheKey(self::CACHE_KEY_IMPRESSIONS, sprintf(self::NAMESPACE_AD_TAG, $videoWaterfallTagId), $reportDay);
        $cacheKeyClicks = $this->getCacheKey(self::CACHE_KEY_CLICKS, sprintf(self::NAMESPACE_AD_TAG, $videoWaterfallTagId), $reportDay);
        $cacheKeyErrors = $this->getCacheKey(self::CACHE_KEY_ERRORS, sprintf(self::NAMESPACE_AD_TAG, $videoWaterfallTagId), $reportDay);
        $cacheKeyBids = $this->getCacheKey(self::CACHE_KEY_BIDS, sprintf(self::NAMESPACE_AD_TAG, $videoWaterfallTagId), $reportDay);

        if (array_key_exists($cacheKeyRequests, $redisReportData)) {
            $this->requests = (int)$redisReportData[$cacheKeyRequests];
        }

        if (array_key_exists($cacheKeyImpressions, $redisReportData)) {
            $this->impressions = (int)$redisReportData[$cacheKeyImpressions];
        }

        if (array_key_exists($cacheKeyClicks, $redisReportData)) {
            $this->clicks = (float)$redisReportData[$cacheKeyClicks];
        }

        if (array_key_exists($cacheKeyErrors, $redisReportData)) {
            $this->errors = (float)$redisReportData[$cacheKeyErrors];
        }

        if (array_key_exists($cacheKeyBids, $redisReportData)) {
            $this->bids = (float)$redisReportData[$cacheKeyBids];
        }

        $this->videoWaterfallTagId = $videoWaterfallTagId;
    }

    /**
     * @return mixed
     */
    public function getVideoWaterfallTagId()
    {
        return $this->videoWaterfallTagId;
    }
}