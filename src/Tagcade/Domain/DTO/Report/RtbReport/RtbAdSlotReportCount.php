<?php

namespace Tagcade\Domain\DTO\Report\RtbReport;


class RtbAdSlotReportCount implements RtbAdSlotReportCountInterface
{
    const KEY_DATE_FORMAT = 'ymd';

    /* cache keys */
    const CACHE_KEY_SLOT_OPPORTUNITY = 'opportunity'; // same "opportunities" key, used with different namespace
    const CACHE_KEY_IMPRESSION = 'impression';
    const CACHE_KEY_PRICE = 'price';

    /* namespace keys */
    const NAMESPACE_AD_SLOT = 'adslot_%d';

    /* all values in hash */
    private $slotOpportunities = 0;
    private $impressions = 0;
    private $price = 0;

    private $adSlotId;

    function __construct($adSlotId, array $redisReportData, $supportMget = true, $day = null)
    {
        $reportDay = $day instanceof \DateTime ? $day : new \DateTime('yesterday');

        if ($supportMget === true) {
            $cacheKeySlotOpportunity = $this->getCacheKey(self::CACHE_KEY_SLOT_OPPORTUNITY, sprintf(self::NAMESPACE_AD_SLOT, $adSlotId), $reportDay);
            $cacheKeyImpression = $this->getCacheKey(self::CACHE_KEY_IMPRESSION, sprintf(self::NAMESPACE_AD_SLOT, $adSlotId), $reportDay);
            $cacheKeyPrice = $this->getCacheKey(self::CACHE_KEY_PRICE, sprintf(self::NAMESPACE_AD_SLOT, $adSlotId), $reportDay);

            if (array_key_exists($cacheKeySlotOpportunity, $redisReportData)) {
                $this->slotOpportunities = (int)$redisReportData[$cacheKeySlotOpportunity];
            }

            if (array_key_exists($cacheKeyImpression, $redisReportData)) {
                $this->impressions = (int)$redisReportData[$cacheKeyImpression];
            }

            if (array_key_exists($cacheKeyPrice, $redisReportData)) {
                $this->price = (float)$redisReportData[$cacheKeyPrice];
            }
        } else {
            $this->slotOpportunities = (int)$redisReportData[0];
            $this->impressions = (int)$redisReportData[1];
            $this->price = (float)$redisReportData[2];
        }

        // set adSlotId
        $this->adSlotId = $adSlotId;
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
    public function getAdSlotId()
    {
        return $this->adSlotId;
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