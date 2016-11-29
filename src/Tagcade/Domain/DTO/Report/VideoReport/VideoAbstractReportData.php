<?php


namespace Tagcade\Domain\DTO\Report\VideoReport;


abstract class VideoAbstractReportData implements VideoRedisReportDataInterface
{
    const KEY_DATE_FORMAT = 'ymd';

    /* cache keys */
    const CACHE_KEY_REQUESTS = 'requests';
    const CACHE_KEY_IMPRESSIONS = 'impressions';
    const CACHE_KEY_CLICKS = 'clicks';
    const CACHE_KEY_ERRORS = 'errors';
    const CACHE_KEY_BIDS = 'bids';
    const CACHE_KEY_BLOCKS = 'blocks';

    /* namespace keys */
    const NAMESPACE_AD_TAG = 'waterfall_tag_%s'; // USING UUID AS ID for video ad tag
    const NAMESPACE_AD_SOURCE = 'demand_tag_%d'; // using normal id for video ad source

    /* all values in hash */
    protected $requests = 0;
    protected $impressions = 0;
    protected $clicks = 0;
    protected $errors = 0;
    protected $bids = 0;
    protected $blocks =0;

    /**
     * @return int
     */
    public function getRequests()
    {
        return $this->requests;
    }

    /**
     * @return int
     */
    public function getImpressions()
    {
        return $this->impressions;
    }

    /**
     * @return int
     */
    public function getClicks()
    {
        return $this->clicks;
    }

    /**
     * @return int
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @return int
     */
    public function getBids()
    {
        return $this->bids;
    }

    /**
     * @return int
     */
    public function getBlocks()
    {
        return $this->blocks;
    }

    /**
     * get CacheKey from $cacheKeyPrefix (e.g opportunity), $namespace (e.g adslot_{id}) and $day
     *
     * @param string $cacheKeyPrefix
     * @param string $namespace
     * @param \DateTime $day
     * @return string the cacheKey
     */
    protected function getCacheKey($cacheKeyPrefix, $namespace, \DateTime $day)
    {
        $keyFormat = '%s:%s:%s'; // cacheKey:namespace:date

        return sprintf($keyFormat, $cacheKeyPrefix, $namespace, $day->format(self::KEY_DATE_FORMAT));
    }
}