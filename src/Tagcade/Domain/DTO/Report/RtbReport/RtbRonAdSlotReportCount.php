<?php

namespace Tagcade\Domain\DTO\Report\RtbReport;


class RtbRonAdSlotReportCount implements RtbRonAdSlotReportCountInterface
{
    const KEY_DATE_FORMAT = 'ymd';

    /* cache keys */
    const CACHE_KEY_SLOT_OPPORTUNITY = 'opportunity'; // same "opportunities" key, used with different namespace
    const CACHE_KEY_IMPRESSION = 'impression';
    const CACHE_KEY_PRICE = 'price';

    /* namespace keys */
    const NAMESPACE_RON_AD_SLOT = 'ron_adslot_%d';
    const NAMESPACE_SEGMENT = 'segment_%d';

    /* all values in hash */
    private $slotOpportunities = 0;
    private $impressions = 0;
    private $price = 0;

    private $ronAdSlotId;

    function __construct($ronAdSlotId, array $redisReportData, $day = null, $segmentId = null)
    {
        $reportDay = $day instanceof \DateTime ? $day : new \DateTime('today');

        $namespace = sprintf(self::NAMESPACE_RON_AD_SLOT, $ronAdSlotId);
        if ($segmentId !== null) {
            $namespace = sprintf($namespace. ":" . self::NAMESPACE_SEGMENT, $segmentId);
        }

        $cacheKeySlotOpportunity = $this->getCacheKey(self::CACHE_KEY_SLOT_OPPORTUNITY, $namespace, $reportDay);
        $cacheKeyImpression = $this->getCacheKey(self::CACHE_KEY_IMPRESSION, $namespace, $reportDay);
        $cacheKeyPrice = $this->getCacheKey(self::CACHE_KEY_PRICE, $namespace, $reportDay);

        if (array_key_exists($cacheKeySlotOpportunity, $redisReportData)) {
            $this->slotOpportunities = (int)$redisReportData[$cacheKeySlotOpportunity];
        }

        if (array_key_exists($cacheKeyImpression, $redisReportData)) {
            $this->impressions = (int)$redisReportData[$cacheKeyImpression];
        }

        if (array_key_exists($cacheKeyPrice, $redisReportData)) {
            $this->price = (float)$redisReportData[$cacheKeyPrice];
        }

        // set ron adSlotId
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
     * @return int
     */
    public function getImpressions()
    {
        return $this->impressions;
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @return int
     */
    public function getRonAdSlotId()
    {
        return $this->ronAdSlotId;
    }

    /**
     * get CacheKey from $cacheKeyPrefix (e.g opportunity), $namespace (e.g adslot_{id}) and $day
     *
     * @param string $cacheKeyPrefix
     * @param string $namespace
     * @param \DateTime $day
     * @return string the cacheKey
     */
    private function getCacheKey($cacheKeyPrefix, $namespace, \DateTime $day)
    {
        $keyFormat = '%s:%s:%s'; // cacheKey:namespace:date

        return sprintf($keyFormat, $cacheKeyPrefix, $namespace, $day->format(self::KEY_DATE_FORMAT));
    }
}