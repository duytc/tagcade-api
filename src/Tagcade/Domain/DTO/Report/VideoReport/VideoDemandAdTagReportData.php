<?php


namespace Tagcade\Domain\DTO\Report\VideoReport;


class VideoDemandAdTagReportData extends VideoAbstractReportData implements VideoDemandAdTagReportDataInterface
{
    protected $videoDemandAdTagId;

    /**
     * VideoDemandAdTagReportData constructor.
     * @param $videoDemandAdTagId
     * @param array $redisReportData
     * @param $day = null
     */
    public function __construct($videoDemandAdTagId, array $redisReportData, $day = null)
    {
        $reportDay = $day instanceof \DateTime ? $day : new \DateTime('today');

        $cacheKeyRequests = $this->getCacheKey(self::CACHE_KEY_REQUESTS, sprintf(self::NAMESPACE_AD_SOURCE, $videoDemandAdTagId), $reportDay);
        $cacheKeyImpressions = $this->getCacheKey(self::CACHE_KEY_IMPRESSIONS, sprintf(self::NAMESPACE_AD_SOURCE, $videoDemandAdTagId), $reportDay);
        $cacheKeyClicks = $this->getCacheKey(self::CACHE_KEY_CLICKS, sprintf(self::NAMESPACE_AD_SOURCE, $videoDemandAdTagId), $reportDay);
        $cacheKeyErrors = $this->getCacheKey(self::CACHE_KEY_ERRORS, sprintf(self::NAMESPACE_AD_SOURCE, $videoDemandAdTagId), $reportDay);
        $cacheKeyBids = $this->getCacheKey(self::CACHE_KEY_BIDS, sprintf(self::NAMESPACE_AD_SOURCE, $videoDemandAdTagId), $reportDay);
        $cacheKeyBlocks = $this->getCacheKey(self::CACHE_KEY_BLOCKS, sprintf(self::NAMESPACE_AD_SOURCE, $videoDemandAdTagId), $reportDay);

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

        if (array_key_exists($cacheKeyBlocks, $redisReportData)) {
            $this->blocks = (float)$redisReportData[$cacheKeyBlocks];
        }

        $this->videoDemandAdTagId = $videoDemandAdTagId;
    }

    /**
     * @return mixed
     */
    public function getVideoDemandAdTagId()
    {
        return $this->videoDemandAdTagId;
    }
}