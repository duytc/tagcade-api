<?php

namespace Tagcade\Domain\DTO\Report\Performance;


class AdTagReportCount implements BaseAdTagReportCountInterface
{
    const CACHE_KEY_OPPORTUNITY            = 'opportunities';
    const CACHE_KEY_FIRST_OPPORTUNITY      = 'first_opportunities';
    const CACHE_KEY_IMPRESSION             = 'impressions';
    const CACHE_KEY_VERIFIED_IMPRESSION    = 'verified_impressions';
    const CACHE_KEY_UNVERIFIED_IMPRESSION  = 'unverified_impressions';
    const CACHE_KEY_BLANK_IMPRESSION       = 'blank_impressions';
    const CACHE_KEY_VOID_IMPRESSION        = 'void_impressions';
    const CACHE_KEY_CLICK                  = 'clicks';
    const CACHE_KEY_REFRESHES              = 'refreshes';
    const CACHE_KEY_PASSBACK               = 'passbacks';
    const CACHE_KEY_FALLBACK               = 'fallbacks'; // legacy name for passbacks is fallbacks
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
    private $refreshes = 0;
    private $forcedPassbacks = 0;

    function __construct(array $reportCounts)
    {
        if (array_key_exists(self::CACHE_KEY_OPPORTUNITY, $reportCounts)) {
            $this->opportunities = $reportCounts[self::CACHE_KEY_OPPORTUNITY];
        }

        if (array_key_exists(self::CACHE_KEY_FIRST_OPPORTUNITY, $reportCounts)) {
            $this->firstOpportunities = $reportCounts[self::CACHE_KEY_FIRST_OPPORTUNITY];
        }

        if (array_key_exists(self::CACHE_KEY_IMPRESSION, $reportCounts)) {
            $this->impressions = $reportCounts[self::CACHE_KEY_IMPRESSION];
        }

        if (array_key_exists(self::CACHE_KEY_VERIFIED_IMPRESSION, $reportCounts)) {
            $this->verifiedImpressions = $reportCounts[self::CACHE_KEY_VERIFIED_IMPRESSION];
        }

        if (array_key_exists(self::CACHE_KEY_UNVERIFIED_IMPRESSION, $reportCounts)) {
            $this->unverifiedImpressions = $reportCounts[self::CACHE_KEY_UNVERIFIED_IMPRESSION];
        }

        if (array_key_exists(self::CACHE_KEY_BLANK_IMPRESSION, $reportCounts)) {
            $this->blankImpressions = $reportCounts[self::CACHE_KEY_BLANK_IMPRESSION];
        }

        if (array_key_exists(self::CACHE_KEY_VOID_IMPRESSION, $reportCounts)) {
            $this->voidImpressions = $reportCounts[self::CACHE_KEY_VOID_IMPRESSION];
        }

        if (array_key_exists(self::CACHE_KEY_CLICK, $reportCounts)) {
            $this->clicks = $reportCounts[self::CACHE_KEY_CLICK];
        }

        if (array_key_exists(self::CACHE_KEY_REFRESHES, $reportCounts)) {
            $this->refreshes = $reportCounts[self::CACHE_KEY_REFRESHES];
        }

        if (array_key_exists(self::CACHE_KEY_PASSBACK, $reportCounts)) {
            $this->passbacks += $reportCounts[self::CACHE_KEY_PASSBACK];
        }

        if (array_key_exists(self::CACHE_KEY_FALLBACK, $reportCounts)) {
            $this->passbacks += $reportCounts[self::CACHE_KEY_FALLBACK];
        }

        if (array_key_exists(self::CACHE_KEY_FORCED_PASSBACK, $reportCounts)) {
            $this->forcedPassbacks = $reportCounts[self::CACHE_KEY_FORCED_PASSBACK];
        }
    }

    /**
     * @inheritdoc
     */
    public function getFirstOpportunityCount()
    {
        return $this->firstOpportunities;
    }

    /**
     * @inheritdoc
     */
    public function getOpportunities()
    {
        return $this->opportunities;
    }

    /**
     * @inheritdoc
     */
    public function getImpressions()
    {
        return $this->impressions;
    }

    /**
     * @inheritdoc
     */
    public function getVerifiedImpressionCount()
    {
        return $this->verifiedImpressions;
    }

    /**
     * @inheritdoc
     */
    public function getPassbackCount()
    {
        return $this->passbacks;
    }

    /**
     * @inheritdoc
     */
    public function getUnverifiedImpressionCount()
    {
        return $this->unverifiedImpressions;
    }

    /**
     * @inheritdoc
     */
    public function getBlankImpressionCount()
    {
        return $this->blankImpressions;
    }

    /**
     * @inheritdoc
     */
    public function getVoidImpressionCount()
    {
        return $this->voidImpressions;
    }

    /**
     * @inheritdoc
     */
    public function getClickCount()
    {
        return $this->clicks;
    }

    /**
     * @inheritdoc
     */
    public function getRefreshesCount()
    {
        return $this->refreshes;
    }

    /**
     * @inheritdoc
     */
    public function getForcedPassbacks()
    {
        return $this->forcedPassbacks;
    }
}