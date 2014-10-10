<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Counter;

use DateTime;

use Tagcade\Model\Core\AdSlot;

/**
 * This counter is only used for testing
 */

class TestEventCounter implements EventCounterInterface
{
    const SLOT_OPPORTUNITIES = 'slotOpportunities';
    const OPPORTUNITIES = 'opportunities';
    const IMPRESSIONS = 'impressions';
    const PASSBACKS = 'passbacks';

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

    /**
     * @inheritdoc
     */
    public function setDate(DateTime $date = null)
    {
        // do nothing
    }

    public function getDate()
    {
        return new DateTime('today');
    }

    public function refreshTestData()
    {
        $this->adSlotData = [];
        $this->adTagData = [];

        foreach($this->adSlots as $adSlot) {
            $slotOpportunities = mt_rand(1000, 1000000);
            $opportunitiesRemaining = $slotOpportunities;

            $this->adSlotData[$adSlot->getId()] = [
                static::SLOT_OPPORTUNITIES => $slotOpportunities,
            ];

            foreach($adSlot->getAdTags() as $adTag) {
                /** @var \Tagcade\Entity\Core\AdTag $adTag */

                $opportunities = $opportunitiesRemaining;
                $passbacks = mt_rand(1, $opportunities);
                $impressions = $opportunities - $passbacks;

                if ($impressions < 0) {
                    $impressions = 0;
                }

                $impressions -= mt_rand(0, $impressions);

                $this->adTagData[$adTag->getId()] = [
                    static::OPPORTUNITIES => $opportunities,
                    static::IMPRESSIONS => $impressions,
                    static::PASSBACKS => $passbacks,
                ];

                $opportunitiesRemaining = $passbacks;
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function getSlotOpportunityCount($slotId)
    {
        if (!isset($this->adSlotData[$slotId][static::SLOT_OPPORTUNITIES])) {
            return false;
        }

        return $this->adSlotData[$slotId][static::SLOT_OPPORTUNITIES];
    }

    /**
     * @inheritdoc
     */
    public function getOpportunityCount($tagId)
    {
        if (!isset($this->adTagData[$tagId][static::OPPORTUNITIES])) {
            return false;
        }

        return $this->adTagData[$tagId][static::OPPORTUNITIES];
    }

    /**
     * @inheritdoc
     */
    public function getImpressionCount($tagId)
    {
        if (!isset($this->adTagData[$tagId][static::IMPRESSIONS])) {
            return false;
        }

        return $this->adTagData[$tagId][static::IMPRESSIONS];
    }

    /**
     * @inheritdoc
     */
    public function getPassbackCount($tagId)
    {
        if (!isset($this->adTagData[$tagId][static::PASSBACKS])) {
            return false;
        }

        return $this->adTagData[$tagId][static::PASSBACKS];
    }
}