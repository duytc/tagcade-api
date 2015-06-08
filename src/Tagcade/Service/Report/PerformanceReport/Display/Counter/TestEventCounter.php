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
    const KEY_OPPORTUNITY            = 'opportunities';
    const KEY_SLOT_OPPORTUNITY       = 'opportunities';
    const KEY_IMPRESSION             = 'impressions';
    const KEY_PASSBACK               = 'passbacks';
    const KEY_FIRST_OPPORTUNITY      = 'first_opportunities';
    const KEY_VERIFIED_IMPRESSION    = 'verified_impressions';
    const KEY_UNVERIFIED_IMPRESSION  = 'unverified_impressions';
    const KEY_BLANK_IMPRESSION       = 'blank_impressions';

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
                static::KEY_SLOT_OPPORTUNITY => $slotOpportunities,
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
                    static::KEY_OPPORTUNITY => $opportunities,
                    static::KEY_IMPRESSION => $impressions,
                    static::KEY_PASSBACK => $passbacks,
                    static::KEY_FIRST_OPPORTUNITY => $firstOpportunities,
                    static::KEY_VERIFIED_IMPRESSION => $verifiedImpressions,
                    static::KEY_UNVERIFIED_IMPRESSION => $unverifiedImpressions,
                    static::KEY_BLANK_IMPRESSION => $blankImpressions,
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
        if (!isset($this->adSlotData[$slotId][static::KEY_SLOT_OPPORTUNITY])) {
            return false;
        }

        return $this->adSlotData[$slotId][static::KEY_SLOT_OPPORTUNITY];
    }

    /**
     * @inheritdoc
     */
    public function getOpportunityCount($tagId)
    {
        if (!isset($this->adTagData[$tagId][static::KEY_OPPORTUNITY])) {
            return false;
        }

        return $this->adTagData[$tagId][static::KEY_OPPORTUNITY];
    }

    /**
     * @inheritdoc
     */
    public function getImpressionCount($tagId)
    {
        if (!isset($this->adTagData[$tagId][static::KEY_IMPRESSION])) {
            return false;
        }

        return $this->adTagData[$tagId][static::KEY_IMPRESSION];
    }

    /**
     * @inheritdoc
     */
    public function getPassbackCount($tagId)
    {
        if (!isset($this->adTagData[$tagId][static::KEY_PASSBACK])) {
            return false;
        }

        return $this->adTagData[$tagId][static::KEY_PASSBACK];
    }

    /**
     * @param int $tagId
     * @return int|bool
     */
    public function getFirstOpportunityCount($tagId)
    {
        if (!isset($this->adTagData[$tagId][static::KEY_FIRST_OPPORTUNITY])) {
            return false;
        }

        return $this->adTagData[$tagId][static::KEY_FIRST_OPPORTUNITY];
    }

    /**
     * @param int $tagId
     * @return int|bool
     */
    public function getVerifiedImpressionCount($tagId)
    {
        if (!isset($this->adTagData[$tagId][static::KEY_VERIFIED_IMPRESSION])) {
            return false;
        }

        return $this->adTagData[$tagId][static::KEY_VERIFIED_IMPRESSION];
    }

    /**
     * @param int $tagId
     * @return int|bool
     */
    public function getUnverifiedImpressionCount($tagId)
    {
        if (!isset($this->adTagData[$tagId][static::KEY_UNVERIFIED_IMPRESSION])) {
            return false;
        }

        return $this->adTagData[$tagId][static::KEY_UNVERIFIED_IMPRESSION];
    }

    /**
     * @param int $tagId
     * @return int|bool
     */
    public function getBlankImpressionCount($tagId)
    {
        if (!isset($this->adTagData[$tagId][static::KEY_BLANK_IMPRESSION])) {
            return false;
        }

        return $this->adTagData[$tagId][static::KEY_BLANK_IMPRESSION];
    }


    protected function seedRandomGenerator()
    {
        list($usec, $sec) = explode(' ', microtime());
        $seed = (float) $sec + ((float) $usec * 100000);

        mt_srand($seed);
    }
}