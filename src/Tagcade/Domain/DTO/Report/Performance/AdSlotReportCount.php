<?php

namespace Tagcade\Domain\DTO\Report\Performance;


use Tagcade\Service\Report\PerformanceReport\Display\Counter\CacheEventCounter;

class AdSlotReportCount implements BaseAdSlotReportCountInterface
{
    private $slotOpportunities = 0;
    private $inBannerRequests = 0;
    private $inBannerImpressions = 0;
    private $inBannerTimeouts = 0;
    private $rtbImpression = 0;
    private $hbRequests = 0;

    function __construct(array $reportCounts)
    {
        if (array_key_exists(CacheEventCounter::CACHE_KEY_SLOT_OPPORTUNITY, $reportCounts)) {
            $this->slotOpportunities = (int)$reportCounts[CacheEventCounter::CACHE_KEY_SLOT_OPPORTUNITY];
        }

        if (array_key_exists(CacheEventCounter::CACHE_KEY_RTB_IMPRESSION, $reportCounts)) {
            $this->rtbImpression = (int)$reportCounts[CacheEventCounter::CACHE_KEY_RTB_IMPRESSION];
        }

        if (array_key_exists(CacheEventCounter::CACHE_KEY_HB_BID_REQUEST, $reportCounts)) {
            $this->hbRequests = (int)$reportCounts[CacheEventCounter::CACHE_KEY_HB_BID_REQUEST];
        }

        if (array_key_exists(CacheEventCounter::CACHE_KEY_IN_BANNER_REQUEST, $reportCounts)) {
            $this->hbRequests = (int)$reportCounts[CacheEventCounter::CACHE_KEY_IN_BANNER_REQUEST];
        }

        if (array_key_exists(CacheEventCounter::CACHE_KEY_IN_BANNER_IMPRESSION, $reportCounts)) {
            $this->hbRequests = (int)$reportCounts[CacheEventCounter::CACHE_KEY_IN_BANNER_IMPRESSION];
        }

        if (array_key_exists(CacheEventCounter::CACHE_KEY_IN_BANNER_TIMEOUT, $reportCounts)) {
            $this->hbRequests = (int)$reportCounts[CacheEventCounter::CACHE_KEY_IN_BANNER_TIMEOUT];
        }
    }

    /**
     * @return mixed
     */
    public function getSlotOpportunities()
    {
        return $this->slotOpportunities;
    }

    /**
     * @return int
     */
    public function getRtbImpression()
    {
        return $this->rtbImpression;
    }

    /**
     * @return int
     */
    public function getHbRequests()
    {
        return $this->hbRequests;
    }

    /**
     * @return int
     */
    public function getInBannerRequests()
    {
        return $this->inBannerRequests;
    }

    /**
     * @return int
     */
    public function getInBannerImpressions()
    {
        return $this->inBannerImpressions;
    }

    /**
     * @return int
     */
    public function getInBannerTimeouts()
    {
        return $this->inBannerTimeouts;
    }
}