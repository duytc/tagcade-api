<?php

namespace Tagcade\Domain\DTO\Report\Performance;


class RonAdTagReportCount implements BaseAdTagReportCountInterface
{
    const CACHE_KEY_OPPORTUNITY            = 'opportunities';
    const CACHE_KEY_FIRST_OPPORTUNITY      = 'first_opportunities';
    const CACHE_KEY_IMPRESSION             = 'impressions';
    const CACHE_KEY_VERIFIED_IMPRESSION    = 'verified_impressions';
    const CACHE_KEY_UNVERIFIED_IMPRESSION  = 'unverified_impressions';
    const CACHE_KEY_BLANK_IMPRESSION       = 'blank_impressions';
    const CACHE_KEY_VOID_IMPRESSION        = 'void_impressions';
    const CACHE_KEY_CLICK                  = 'clicks';
    const CACHE_KEY_PASSBACK               = 'passbacks'; // legacy name is fallbacks
    const CACHE_KEY_FORCED_PASSBACK        = 'forced_passbacks'; // not counted yet for now

    private $firstOpportunities = 0;
    private $opportunities = 0;
    private $verifiedImpressions = 0;
    private $impressions = 0;
    private $passbacks = 0;
    private $unverifiedImpressions = 0;
    private $blankImpressions = 0;
    private $voidImpressions = 0;
    private $clicks = 0;
    private $forcedPassbacks = 0;

    private $ronTagId;

    const NAMESPACE_RON_AD_TAG             = 'ron_tag_%d';
    const NAMESPACE_APPEND_SEGMENT         = 'segment_%d';


    function __construct($ronTagId, array $redisReportData, $segment = null)
    {
        $this->ronTagId = $ronTagId;
        $namespace = sprintf(self::NAMESPACE_RON_AD_TAG, $ronTagId);
        if (null !== $segment) {
            $namespace = sprintf($namespace . ':' .  self::NAMESPACE_APPEND_SEGMENT, $segment);
        }

        $today = new \DateTime('today');

        $namespaceAndToday = sprintf('%s:%s', $namespace, $today->format('ymd'));

        $cacheKeyOpportunity = sprintf('%s:%s', self::CACHE_KEY_OPPORTUNITY, $namespaceAndToday);
        if (array_key_exists($cacheKeyOpportunity, $redisReportData)) {
            $this->opportunities = (int)$redisReportData[$cacheKeyOpportunity];
        }

        $cacheKeyOpportunity = sprintf('%s:%s', self::CACHE_KEY_FIRST_OPPORTUNITY, $namespaceAndToday);
        if (array_key_exists($cacheKeyOpportunity, $redisReportData)) {
            $this->firstOpportunities = (int)$redisReportData[$cacheKeyOpportunity];
        }

        $cacheKeyOpportunity = sprintf('%s:%s', self::CACHE_KEY_IMPRESSION, $namespaceAndToday);
        if (array_key_exists($cacheKeyOpportunity, $redisReportData)) {
            $this->impressions = (int)$redisReportData[$cacheKeyOpportunity];
        }

        $cacheKeyOpportunity = sprintf('%s:%s', self::CACHE_KEY_VERIFIED_IMPRESSION, $namespaceAndToday);
        if (array_key_exists($cacheKeyOpportunity, $redisReportData)) {
            $this->verifiedImpressions = (int)$redisReportData[$cacheKeyOpportunity];
        }

        $cacheKeyOpportunity = sprintf('%s:%s', self::CACHE_KEY_UNVERIFIED_IMPRESSION, $namespaceAndToday);
        if (array_key_exists($cacheKeyOpportunity, $redisReportData)) {
            $this->unverifiedImpressions = (int)$redisReportData[$cacheKeyOpportunity];
        }

        $cacheKeyOpportunity = sprintf('%s:%s', self::CACHE_KEY_BLANK_IMPRESSION, $namespaceAndToday);
        if (array_key_exists($cacheKeyOpportunity, $redisReportData)) {
            $this->blankImpressions = (int)$redisReportData[$cacheKeyOpportunity];
        }

        $cacheKeyOpportunity = sprintf('%s:%s', self::CACHE_KEY_VOID_IMPRESSION, $namespaceAndToday);
        if (array_key_exists($cacheKeyOpportunity, $redisReportData)) {
            $this->voidImpressions = (int)$redisReportData[$cacheKeyOpportunity];
        }

        $cacheKeyOpportunity = sprintf('%s:%s', self::CACHE_KEY_CLICK, $namespaceAndToday);
        if (array_key_exists($cacheKeyOpportunity, $redisReportData)) {
            $this->clicks = (int)$redisReportData[$cacheKeyOpportunity];
        }

        $cacheKeyOpportunity = sprintf('%s:%s', self::CACHE_KEY_PASSBACK, $namespaceAndToday);
        if (array_key_exists($cacheKeyOpportunity, $redisReportData)) {
            $this->passbacks = (int)$redisReportData[$cacheKeyOpportunity];
        }

        $cacheKeyOpportunity = sprintf('%s:%s', self::CACHE_KEY_FORCED_PASSBACK, $namespaceAndToday);
        if (array_key_exists($cacheKeyOpportunity, $redisReportData)) {
            $this->forcedPassbacks = (int)$redisReportData[$cacheKeyOpportunity];
        }

    }

    public function getFirstOpportunityCount()
    {
        return $this->firstOpportunities;
    }

    public function getOpportunities()
    {
        return $this->opportunities;
    }

    public function getImpressions()
    {
        return $this->impressions;
    }

    public function getVerifiedImpressionCount()
    {
        return $this->verifiedImpressions;
    }

    public function getPassbackCount()
    {
        return $this->passbacks;
    }

    public function getUnverifiedImpressionCount()
    {
        return $this->unverifiedImpressions;
    }

    public function getBlankImpressionCount()
    {
        return $this->blankImpressions;
    }

    public function getVoidImpressionCount()
    {
        return $this->voidImpressions;
    }

    public function getClickCount()
    {
        return $this->clicks;
    }

    public function getForcedPassbacks()
    {
        return $this->forcedPassbacks;
    }
}