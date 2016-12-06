<?php

namespace Tagcade\Domain\DTO\Report\Performance;


use Tagcade\Service\Report\PerformanceReport\Display\Counter\CacheEventCounter;

class AccountReportCount implements BaseAdSlotReportCountInterface
{
    private $slotOpportunities = 0;
    private $opportunities = 0;
    private $impression = 0;
    private $rtbImpression = 0;
    private $hbRequests = 0;
    private $passbacks = 0;
    private $inBannerRequests;
    private $inBannerTimeouts;
    private $inBannerImpressions;

    function __construct(array $reportCounts)
    {
        if (array_key_exists(CacheEventCounter::CACHE_KEY_ACC_SLOT_OPPORTUNITY, $reportCounts)) {
            $this->slotOpportunities = (int)$reportCounts[CacheEventCounter::CACHE_KEY_ACC_SLOT_OPPORTUNITY];
        }

        if (array_key_exists(CacheEventCounter::CACHE_KEY_ACC_OPPORTUNITY, $reportCounts)) {
            $this->opportunities = (int)$reportCounts[CacheEventCounter::CACHE_KEY_ACC_OPPORTUNITY];
        }

        if (array_key_exists(CacheEventCounter::CACHE_KEY_RTB_IMPRESSION, $reportCounts)) {
            $this->rtbImpression = (int)$reportCounts[CacheEventCounter::CACHE_KEY_RTB_IMPRESSION];
        }

        if (array_key_exists(CacheEventCounter::CACHE_KEY_HB_BID_REQUEST, $reportCounts)) {
            $this->hbRequests = (int)$reportCounts[CacheEventCounter::CACHE_KEY_HB_BID_REQUEST];
        }

        if (array_key_exists(CacheEventCounter::CACHE_KEY_IMPRESSION, $reportCounts)) {
            $this->impression = (int)$reportCounts[CacheEventCounter::CACHE_KEY_IMPRESSION];
        }

        if (array_key_exists(CacheEventCounter::CACHE_KEY_PASSBACK, $reportCounts)) {
            $this->passbacks += (int)$reportCounts[CacheEventCounter::CACHE_KEY_PASSBACK];
        }

        if (array_key_exists(CacheEventCounter::CACHE_KEY_FALLBACK, $reportCounts)) {
            $this->passbacks += (int)$reportCounts[CacheEventCounter::CACHE_KEY_FALLBACK];
        }

        if (array_key_exists(CacheEventCounter::CACHE_KEY_IN_BANNER_IMPRESSION, $reportCounts)) {
            $this->inBannerImpressions = (int)$reportCounts[CacheEventCounter::CACHE_KEY_IN_BANNER_IMPRESSION];
        }

        if (array_key_exists(CacheEventCounter::CACHE_KEY_IN_BANNER_TIMEOUT, $reportCounts)) {
            $this->inBannerTimeouts = (int)$reportCounts[CacheEventCounter::CACHE_KEY_IN_BANNER_TIMEOUT];
        }

        if (array_key_exists(CacheEventCounter::CACHE_KEY_IN_BANNER_REQUEST, $reportCounts)) {
            $this->inBannerRequests = (int)$reportCounts[CacheEventCounter::CACHE_KEY_IN_BANNER_REQUEST];
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
    public function getOpportunities()
    {
        return $this->opportunities;
    }

    /**
     * @return int
     */
    public function getImpression()
    {
        return $this->impression;
    }

    /**
     * @return int
     */
    public function getPassbacks()
    {
        return $this->passbacks;
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
    public function getInBannerTimeouts()
    {
        return $this->inBannerTimeouts;
    }

    /**
     * @return int
     */
    public function getInBannerImpressions()
    {
        return $this->inBannerImpressions;
    }
}