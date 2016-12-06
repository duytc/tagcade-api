<?php

namespace Tagcade\Service\Report\RtbReport\Counter;


use Tagcade\Domain\DTO\Report\RtbReport\RtbAdSlotReportCount;
use Tagcade\Domain\DTO\Report\RtbReport\RtbRonAdSlotReportCount;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\ReportableAdSlotInterface;
use Tagcade\Model\Core\RonAdSlotInterface;
use Tagcade\Model\Core\SegmentInterface;

/**
 * This counter is only used for testing
 */
class RtbTestEventCounter extends RtbAbstractEventCounter
{
    const KEY_DATE_FORMAT = 'ymd';

    /* cache keys */
    const CACHE_KEY_SLOT_OPPORTUNITY = 'opportunity';
    const CACHE_KEY_IMPRESSION = 'impression';
    const CACHE_KEY_PRICE = 'price';

    /* namespace keys */
    const NAMESPACE_AD_SLOT = 'adslot_%d';
    const NAMESPACE_ACCOUNT = 'account_%d';
    const NAMESPACE_RON_AD_SLOT = 'ron_adslot_%d';
    const NAMESPACE_SEGMENT = 'segment_%d';

    /* redis hash namespace for rtb */
    const REDIS_HASH_RTB_EVENT_COUNT = 'rtb_event_processor:event_count';

    /* all ad slots */
    protected $adSlots;

    /* all ron ad slots */
    protected $ronAdSlots;

    /* ad slots data as
        [
            adSlotId => [
                opportunity,
                impression,
                price
            ],
            ...
        ]
    */
    protected $adSlotData = [];
    protected $accountData = [];
    /* ron ad slots data as
        [
            ronAdSlotId => [
                opportunity,
                impression,
                price
            ],
            ...
        ]
    */
    protected $ronAdSlotData = [];

    /* ron ad slots segment data as
        [
            ronAdSlotId => [
                segmentId => [
                    opportunity,
                    impression,
                    price
                ],
                ...],
            ...
        ]
    */
    protected $ronAdSlotSegmentData = [];

    /**
     * @param ReportableAdSlotInterface[] $adSlots
     * @param RonAdSlotInterface[] $ronAdSlots
     */
    public function __construct(array $adSlots, array $ronAdSlots)
    {
        $this->adSlots = $adSlots;
        $this->ronAdSlots = $ronAdSlots;
    }

    /**
     * get adSlotData
     * @return array
     */
    public function getAdSlotData()
    {
        return $this->adSlotData;
    }

    /**
     * @return array
     */
    public function getAccountData()
    {
        return $this->accountData;
    }

    /**
     * get ronAdSlotData
     * @return array
     */
    public function getRonAdSlotData()
    {
        return $this->ronAdSlotData;
    }

    /**
     * get ronAdSlotSegmentData
     * @return array
     */
    public function getRonAdSlotSegmentData()
    {
        return $this->ronAdSlotSegmentData;
    }

    /**
     * refresh random test data for all adSlots
     */
    public function refreshTestData()
    {
        $this->seedRandomGenerator();

        $slotOpportunities = mt_rand(1000, 100000);
        $impression = mt_rand(1000, $slotOpportunities);
        $price = round((floatval(mt_rand(1, 100)) / 33), 4);
        $adSlotOpp = $this->distributeValueToArray($slotOpportunities, count($this->adSlots));
        $adSlotImp = $this->distributeValueToArray($impression, count($this->adSlots));
        // create test data for all ad slots(adSlotData[])
        $this->adSlotData = [];

        foreach ($this->adSlots as $index=>$adSlot) {
            $publisherId = $adSlot->getSite()->getPublisher()->getId();
            $accountCacheNamespace = $this->getNamespace(self::NAMESPACE_ACCOUNT, $publisherId);
            if (!array_key_exists($publisherId, $this->accountData)) {
                $this->accountData[$publisherId] = array (
                    $this->getCacheKey(self::CACHE_KEY_SLOT_OPPORTUNITY, $accountCacheNamespace, $this->getDate()) => 0,
                    $this->getCacheKey(self::CACHE_KEY_IMPRESSION, $accountCacheNamespace, $this->getDate()) => 0,
                    $this->getCacheKey(self::CACHE_KEY_PRICE, $accountCacheNamespace, $this->getDate()) => 0
                );
            }

            // create adSlotData
            $adSlotCacheNamespace = $this->getNamespace(self::NAMESPACE_AD_SLOT, $adSlot->getId());
            $cacheKeySlotOpportunity = $this->getCacheKey(self::CACHE_KEY_SLOT_OPPORTUNITY, $adSlotCacheNamespace, $this->getDate());
            $cacheKeyImpression = $this->getCacheKey(self::CACHE_KEY_IMPRESSION, $adSlotCacheNamespace, $this->getDate());
            $cacheKeyPrice = $this->getCacheKey(self::CACHE_KEY_PRICE, $adSlotCacheNamespace, $this->getDate());

            $this->adSlotData[$adSlot->getId()] = [
                $cacheKeySlotOpportunity => $adSlotOpp[$index],
                $cacheKeyImpression => $adSlotImp[$index],
                $cacheKeyPrice => $price,
            ];

            $this->accountData[$publisherId][$this->getCacheKey(self::CACHE_KEY_SLOT_OPPORTUNITY, $accountCacheNamespace, $this->getDate())] += $adSlotOpp[$index];
            $this->accountData[$publisherId][$this->getCacheKey(self::CACHE_KEY_IMPRESSION, $accountCacheNamespace, $this->getDate())] += $adSlotImp[$index];
        }

        // create test data for all ron AdSlots(ronAdSlotData[]) , ron Ad Slot Segment Data(ronAdSlotSegmentData[]) for each ronAdSlot
        $this->ronAdSlotData = [];
        $this->ronAdSlotSegmentData = [];

        $ronSlotOpp = $this->distributeValueToArray($slotOpportunities, count($this->ronAdSlots));
        $ronSlotImp = $this->distributeValueToArray($impression, count($this->ronAdSlots));
        foreach ($this->ronAdSlots as $index=>$ronAdSlot) {
            if (!$ronAdSlot instanceof RonAdSlotInterface) {
                continue;
            }

            // create ronAdSlotData
            $ronAdSlotCacheNamespace = $this->getNamespace(self::NAMESPACE_RON_AD_SLOT, $ronAdSlot->getId());
            $cacheKeyRonSlotOpportunity = $this->getCacheKey(self::CACHE_KEY_SLOT_OPPORTUNITY, $ronAdSlotCacheNamespace, $this->getDate());
            $cacheKeyRonImpression = $this->getCacheKey(self::CACHE_KEY_IMPRESSION, $ronAdSlotCacheNamespace, $this->getDate());
            $cacheKeyRonPrice = $this->getCacheKey(self::CACHE_KEY_PRICE, $ronAdSlotCacheNamespace, $this->getDate());

            $this->ronAdSlotData[$ronAdSlot->getId()] = [
                $cacheKeyRonSlotOpportunity => $ronSlotOpp[$index],
                $cacheKeyRonImpression => $ronSlotImp[$index],
                $cacheKeyRonPrice => $price,
            ];

            // also, create ronAdSlotSegmentData for each ronAdSlot
            $segments = $ronAdSlot->getSegments();
            $segmentsCount = count($segments);

            if ($segmentsCount < 1) {
                continue;
            }

            //// create ronSlotSegmentData array depend on ronAdSlot opportunity as total opportunity
            $segmentOpportunities = $this->distributeValueToArray($slotOpportunities, $segmentsCount);
            $segmentImpressions = $this->distributeValueToArray($impression, $segmentsCount);
            $segmentPrices = $this->distributeValueToArray($price, $segmentsCount);
            $i = 0;
            foreach ($segments as $segment) {
                /** @var SegmentInterface $segment */
                $ronAdSlotSegmentCacheNamespace = $this->getNamespace(self::NAMESPACE_RON_AD_SLOT, $ronAdSlot->getId(), self::NAMESPACE_SEGMENT, $segment->getId());
                $cacheKeyRonSegmentSlotOpportunity = $this->getCacheKey(self::CACHE_KEY_SLOT_OPPORTUNITY, $ronAdSlotSegmentCacheNamespace, $this->getDate());
                $cacheKeyRonSegmentImpression = $this->getCacheKey(self::CACHE_KEY_IMPRESSION, $ronAdSlotSegmentCacheNamespace, $this->getDate());
                $cacheKeyRonSegmentPrice = $this->getCacheKey(self::CACHE_KEY_PRICE, $ronAdSlotSegmentCacheNamespace, $this->getDate());

                $this->ronAdSlotSegmentData[$ronAdSlot->getId()][$segment->getId()] = [
                    $cacheKeyRonSegmentSlotOpportunity => $segmentOpportunities[$i],
                    $cacheKeyRonSegmentImpression => $segmentImpressions[$i],
                    $cacheKeyRonSegmentPrice => $segmentPrices[$i],
                ];

                $i++;
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function getRtbAdSlotReport($adSlotId, $supportMGet = false)
    {
        // NOTICE: implement this to create RtbAdSlotReport by random value instead of get from cache!!!
        /* build report from $this->adSlotData */
        return new RtbAdSlotReportCount($adSlotId, $this->getAdSlotData()[$adSlotId], $this->getDate());
    }

    /**
     * @inheritdoc
     */
    public function getRtbAdSlotReports(array $adSlotIds, $supportMGet = false)
    {
        // NOTICE: implement this to create RtbAdSlotReports by random value instead of get from cache!!!
        /* build reports from $this->adSlotData */
        $reports = [];
        foreach ($adSlotIds as $id) {
            $reports[] = new RtbAdSlotReportCount($id, $this->getAdSlotData()[$id]);
        }

        return $reports;
    }

    /**
     * @inheritdoc
     */
    public function getRtbRonAdSlotReport($ronAdSlotId, $segmentId = null, $supportMGet = false)
    {
        // NOTICE: implement this to create RtbRonAdSlotReport by random value instead of get from cache!!!
        /* build report from $this->ronAdSlotData or $this->ronAdSlotSegmentData (if segmentId is not null) */
        return is_null($segmentId)
            ? new RtbRonAdSlotReportCount($ronAdSlotId, $this->getRonAdSlotData()[$ronAdSlotId], $this->getDate()) // for ron ad slot
            : new RtbRonAdSlotReportCount($ronAdSlotId, $this->getRonAdSlotSegmentData()[$ronAdSlotId][$segmentId], $this->getDate(), $segmentId); // for ron ad slot segment
    }

    /**
     * @inheritdoc
     */
    public function getRtbRonAdSlotReports(array $ronAdSlotIds, $segmentId = null, $supportMGet = false)
    {
        // NOTICE: implement this to create RtbRonAdSlotReports by random value instead of get from cache!!!
        /* build reports from $this->ronAdSlotData */
        $reports = [];
        foreach ($ronAdSlotIds as $id) {
            $reports[] = new RtbRonAdSlotReportCount($id, $this->getAdSlotData()[$id], $this->getDate(), $segmentId);
        }

        return $reports;
    }

    protected function seedRandomGenerator()
    {
        list($usec, $sec) = explode(' ', microtime());
        $seed = (float)$sec + ((float)$usec * 100000);

        mt_srand($seed);
    }

    /**
     * get Namespace from namespaceFormat and id, optional with appendingFormat and appendingId
     *
     * @param $namespaceFormat
     * @param $id
     * @param null $appendingFormat
     * @param null $appendingId
     * @return string
     */
    public function getNamespace($namespaceFormat, $id, $appendingFormat = null, $appendingId = null)
    {
        $namespace = sprintf($namespaceFormat, $id);

        return $this->appendNamespace($namespace, $appendingFormat, $appendingId);
    }

    /**
     * Does append namespace if id != null. Otherwise returning original namespace
     * @param $namespace
     * @param string|null $appendFormat
     * @param int|null $id
     * @return string
     */
    protected function appendNamespace($namespace, $appendFormat = null, $id = null)
    {
        return (null !== $id && null !== $appendFormat) ? sprintf($namespace . ':' . $appendFormat, $id) : $namespace;
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

    /**
     * distribute Value To Array by random and keep total value not changed
     *
     * @param $value
     * @param $arraySize
     * @return array that has size = $arraySize
     */
    private function distributeValueToArray($value, $arraySize)
    {

        if (!is_int($arraySize) || $arraySize < 0) {
            throw new InvalidArgumentException('expect a positive array size');
        }

        if ($arraySize < 2) {
            return array($value);
        }

        $maxEachItem = floor(100 / $arraySize);

        $result = [];
        for ($i = 0; $i < $arraySize - 1; $i++) {
            $tmpVal = mt_rand(0, $maxEachItem);
            $result[] = round($tmpVal * $value / 100);
        }

        $currentTotal = array_sum($result);
        $result[] = $value - $currentTotal;

        return $result;
    }
}
