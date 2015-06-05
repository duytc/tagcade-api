<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Counter;

use DateTime;

use Doctrine\Common\Cache\Cache;
use Tagcade\Model\Core\AdSlot;

/**
 * This counter is only used for testing
 */

class TestEventCounter extends AbstractEventCounter
{
    const CACHE_KEY_OPPORTUNITY    = 'opportunities';
    const CACHE_KEY_SLOT_OPPORTUNITY    = 'opportunities';
    const CACHE_KEY_IMPRESSION     = 'impressions';
    const CACHE_KEY_PASSBACK       = 'passbacks';
    const CACHE_KEY_FIRST_OPPORTUNITY      = 'first_opportunities';
    const CACHE_KEY_VERIFIED_IMPRESSION    = 'verified_impressions';
    const CACHE_KEY_UNVERIFIED_IMPRESSION  = 'unverified_impressions';
    const CACHE_KEY_BLANK_IMPRESSION       = 'blank_impressions';

    protected $adSlots;
    protected $adSlotData = [];
    protected $adTagData = [];

    /**
     * @param AdSlot[] $adSlots
     */
    public function __construct(array $adSlots)
    {
        $this->adSlots = $adSlots;
    }

    public function refreshTestData()
    {
        $this->adSlotData = [];
        $this->adTagData = [];

        foreach($this->adSlots as $adSlot) {
            $this->seedRandomGenerator();

            $slotOpportunities = mt_rand(1000, 100000);
            $opportunitiesRemaining = $slotOpportunities;

            $this->adSlotData[$adSlot->getId()] = [
                static::CACHE_KEY_SLOT_OPPORTUNITY => $slotOpportunities,
            ];

            foreach($adSlot->getAdTags() as $adTag) {
                /** @var \Tagcade\Entity\Core\AdTag $adTag */

                $opportunities = $opportunitiesRemaining;
                $passbacks = mt_rand(1, $opportunities);
                $impressions = (int)($opportunities - $passbacks);

                if ($impressions < 0) {
                    $impressions = 0;
                }

                $firstOpportunities = mt_rand(0, round($opportunities/2));
                $verifiedImpressions = mt_rand(0, $impressions);
                $unverifiedImpressions = mt_rand(0, ($impressions - $verifiedImpressions));
                $blankImpressions = (int)(($impressions - $verifiedImpressions) - $unverifiedImpressions);

                // can be used to simulate "missing impressions"
                //$impressions -= mt_rand(0, $impressions);

                $this->adTagData[$adTag->getId()] = [
                    static::CACHE_KEY_OPPORTUNITY => $opportunities,
                    static::CACHE_KEY_IMPRESSION => abs($impressions),
                    static::CACHE_KEY_PASSBACK => $passbacks,
                    static::CACHE_KEY_FIRST_OPPORTUNITY => $firstOpportunities,
                    static::CACHE_KEY_VERIFIED_IMPRESSION => $verifiedImpressions,
                    static::CACHE_KEY_UNVERIFIED_IMPRESSION => $unverifiedImpressions,
                    static::CACHE_KEY_BLANK_IMPRESSION => $blankImpressions,
                ];

                $opportunitiesRemaining = $passbacks;
            }
        }
    }

    public function getAdSlotData()
    {
        return $this->adSlotData;
    }

    public function getAdTagData()
    {
        return $this->adTagData;
    }

    /**
     * @inheritdoc
     */
    public function getSlotOpportunityCount($slotId)
    {
        if (!isset($this->adSlotData[$slotId][static::CACHE_KEY_SLOT_OPPORTUNITY])) {
            return false;
        }

        return $this->adSlotData[$slotId][static::CACHE_KEY_SLOT_OPPORTUNITY];
    }

    /**
     * @inheritdoc
     */
    public function getOpportunityCount($tagId)
    {
        if (!isset($this->adTagData[$tagId][static::CACHE_KEY_OPPORTUNITY])) {
            return false;
        }

        return $this->adTagData[$tagId][static::CACHE_KEY_OPPORTUNITY];
    }

    /**
     * @inheritdoc
     */
    public function getImpressionCount($tagId)
    {
        if (!isset($this->adTagData[$tagId][static::CACHE_KEY_IMPRESSION])) {
            return false;
        }

        $impCount = $this->adTagData[$tagId][static::CACHE_KEY_IMPRESSION];

        if ($impCount < 0) {
            $i = 0;
        }
        return $impCount;
    }

    /**
     * @inheritdoc
     */
    public function getPassbackCount($tagId)
    {
        if (!isset($this->adTagData[$tagId][static::CACHE_KEY_PASSBACK])) {
            return false;
        }

        return $this->adTagData[$tagId][static::CACHE_KEY_PASSBACK];
    }

    /**
     * @param int $tagId
     * @return int|bool
     */
    public function getFirstOpportunityCount($tagId)
    {
        if (!isset($this->adTagData[$tagId][static::CACHE_KEY_FIRST_OPPORTUNITY])) {
            return false;
        }

        return $this->adTagData[$tagId][static::CACHE_KEY_FIRST_OPPORTUNITY];
    }

    /**
     * @param int $tagId
     * @return int|bool
     */
    public function getVerifiedImpressionCount($tagId)
    {
        if (!isset($this->adTagData[$tagId][static::CACHE_KEY_VERIFIED_IMPRESSION])) {
            return false;
        }

        return $this->adTagData[$tagId][static::CACHE_KEY_VERIFIED_IMPRESSION];
    }

    /**
     * @param int $tagId
     * @return int|bool
     */
    public function getUnverifiedImpressionCount($tagId)
    {
        if (!isset($this->adTagData[$tagId][static::CACHE_KEY_UNVERIFIED_IMPRESSION])) {
            return false;
        }

        return $this->adTagData[$tagId][static::CACHE_KEY_UNVERIFIED_IMPRESSION];
    }

    /**
     * @param int $tagId
     * @return int|bool
     */
    public function getBlankImpressionCount($tagId)
    {
        if (!isset($this->adTagData[$tagId][static::CACHE_KEY_BLANK_IMPRESSION])) {
            return false;
        }

        return $this->adTagData[$tagId][static::CACHE_KEY_BLANK_IMPRESSION];
    }


    protected function seedRandomGenerator()
    {
        list($usec, $sec) = explode(' ', microtime());
        $seed = (float) $sec + ((float) $usec * 100000);

        mt_srand($seed);
    }
}