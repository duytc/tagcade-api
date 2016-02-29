<?php

namespace Tagcade\Domain\DTO\Report\Performance;


class RonAdSlotReportCount implements BaseAdSlotReportCountInterface
{
    const CACHE_KEY_SLOT_OPPORTUNITY = 'opportunities';
    const NAMESPACE_RON_AD_SLOT            = 'ron_slot_%d';
    const NAMESPACE_APPEND_SEGMENT         = 'segment_%d';

    const REDIS_HASH_EVENT_COUNT           = 'event_processor:event_count';

    private $slotOpportunities = 0;
    private $rtbImpression = 0;

    /**
     * @var
     */
    private $ronAdSlotId;

    function __construct($ronAdSlotId, array $redisReportData, $segment = null)
    {
        $namespace = sprintf(self::NAMESPACE_RON_AD_SLOT, $ronAdSlotId);
        if (null !== $segment) {
            $namespace = sprintf($namespace . ':' .  self::NAMESPACE_APPEND_SEGMENT, $segment);
        }

        $today = new \DateTime('today');

        $namespaceAndToday = sprintf('%s:%s', $namespace, $today->format('ymd'));

        $cacheKeySlotOpportunity = sprintf('%s:%s', self::CACHE_KEY_SLOT_OPPORTUNITY, $namespaceAndToday);
        if (array_key_exists($cacheKeySlotOpportunity, $redisReportData)) {
            $this->slotOpportunities = (int)$redisReportData[$cacheKeySlotOpportunity];
        }

        $this->ronAdSlotId = $ronAdSlotId;
    }


    /**
     * @return mixed
     */
    public function getSlotOpportunities()
    {
        return $this->slotOpportunities;
    }

    /**
     * @return mixed
     */
    public function getRonAdSlotId()
    {
        return $this->ronAdSlotId;
    }

    public function getRtbImpression()
    {
        return $this->rtbImpression;
    }

    /**
     * @param int $rtbImpression
     */
    public function setRtbImpression($rtbImpression)
    {
        $this->rtbImpression = $rtbImpression;
    }



}