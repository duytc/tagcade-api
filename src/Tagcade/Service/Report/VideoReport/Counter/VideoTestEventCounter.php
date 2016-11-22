<?php


namespace Tagcade\Service\Report\VideoReport\Counter;


use DateTime;
use Tagcade\Domain\DTO\Report\VideoReport\VideoDemandAdTagReportData;
use Tagcade\DomainManager\VideoDemandAdTagManagerInterface;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\VideoDemandAdTagInterface;
use Tagcade\Model\Core\VideoWaterfallTagInterface;
use Tagcade\Model\Core\VideoWaterfallTagItemInterface;

class VideoTestEventCounter extends VideoAbstractEventCounter
{
    const KEY_REQUESTS = 'requests';
    const KEY_IMPRESSIONS = 'impressions';
    const KEY_CLICKS = 'clicks';
    const KEY_ERRORS = 'errors';
    const KEY_BIDS = 'bids';
    const KEY_BLOCKS = 'blocks';

    /** @var array|VideoWaterfallTagInterface[] */
    protected $videoWaterfallTags;

    /* video ad tag data as
        [
            videoWaterfallTagId => [
                request,
                bid,
                click,
                error
            ],
            ...
        ]
    */
    protected $videoWaterfallTagData;

    /* video ad source data as
        [
            videoDemandAdTagId => [
                request,
                bid,
                impression,
                click,
                error
            ],
            ...
        ]
    */
    protected $videoDemandAdTagData;

    /**
     * @var VideoDemandAdTagManagerInterface
     */
    protected $videoDemandAdTagManager;

    /**
     * VideoTestEventCounter constructor.
     * @param array|VideoWaterfallTagItemInterface[] $videoWaterfallTags
     * @param VideoDemandAdTagManagerInterface $videoDemandAdTagManager
     */
    public function __construct(array $videoWaterfallTags, VideoDemandAdTagManagerInterface $videoDemandAdTagManager)
    {
        $this->videoWaterfallTags = $videoWaterfallTags;
        $this->videoDemandAdTagManager = $videoDemandAdTagManager;
        $this->setDate(new \DateTime('today'));
    }

    /**
     * get All generated VideoWaterfallTags Data
     *
     * @return array
     */
    public function getAllVideoWaterfallTagsData()
    {
        return $this->videoWaterfallTagData;
    }

    /**
     * get All VideoDemandAdTags Data
     *
     * @return array
     */
    public function getAllVideoDemandAdTagsData()
    {
        return $this->videoDemandAdTagData;
    }

    /**
     * refresh Test Data randomly
     *
     * @param int $minAdTagRequests
     * @param int $maxAdTagRequests
     * @param null $date
     */
    public function refreshTestData($minAdTagRequests, $maxAdTagRequests, $date = null)
    {
        if ($date instanceof DateTime) {
            $this->setDate($date);
        }

        $this->videoWaterfallTagData = [];
        $this->videoDemandAdTagData = [];

        /** @var VideoWaterfallTagInterface $videoWaterfallTag */
        foreach ($this->videoWaterfallTags as $videoWaterfallTag) {
            $this->seedRandomGenerator();

            /* 1. generate random data for video ad tags */
            $adTagRequests = mt_rand($minAdTagRequests, $maxAdTagRequests);
            $adTagErrors = mt_rand(0, $adTagRequests * 0.1);

            $adTagImpressions = $adTagRequests - $adTagErrors;

            /* 2. create all video ad tags data */
            $adTagNameSpace = $this->getNamespace(self::NAMESPACE_WATERFALL_AD_TAG, $videoWaterfallTag->getUuid());

            // make sure video ad tag uuid is valid
            $videoWaterfallTagUuid = $videoWaterfallTag->getUuid();
            if (!$this->isValidUuidV4($videoWaterfallTagUuid)) {
                continue; // skip on video ad tag has an invalid uuid (also skip generating for ad sources)!!!
            }

            $this->videoWaterfallTagData[$videoWaterfallTagUuid][$this->getCacheKey(self::KEY_REQUESTS, $adTagNameSpace)] = $adTagRequests;
            $this->videoWaterfallTagData[$videoWaterfallTagUuid][$this->getCacheKey(self::KEY_ERRORS, $adTagNameSpace)] = $adTagErrors;
            $this->videoWaterfallTagData[$videoWaterfallTagUuid][$this->getCacheKey(self::KEY_BIDS, $adTagNameSpace)] = $adTagImpressions;

            /* 3. generate random data for video ad sources */
            $demandAdTags = $this->videoDemandAdTagManager->getVideoDemandAdTagsForVideoWaterfallTag($videoWaterfallTag);
            $demandAdTagImpressionDatas = $this->distributeValueToArray($adTagImpressions, count($demandAdTags));

            /* 4. create all video ad sources data */
            /** @var VideoDemandAdTagInterface $demandAdTag */
            foreach ($demandAdTags as $index => $demandAdTag) {
                $demandAdTagNameSpace = $this->getNamespace(self::NAMESPACE_DEMAND_AD_TAG, $demandAdTag->getId());
                $demandAdTagImpressions = $demandAdTagImpressionDatas[$index];
                $demandAdTagBid = mt_rand($demandAdTagImpressions, $adTagRequests);
                $demandAdTagRequest = mt_rand($demandAdTagBid, $adTagRequests);
                $demandAdTagBlocks = mt_rand($adTagRequests - $demandAdTagBid, $adTagRequests);

                $this->videoDemandAdTagData[$demandAdTag->getId()][$this->getCacheKey(self::KEY_REQUESTS, $demandAdTagNameSpace)] = $demandAdTagRequest;
                $this->videoDemandAdTagData[$demandAdTag->getId()][$this->getCacheKey(self::KEY_IMPRESSIONS, $demandAdTagNameSpace)] = $demandAdTagImpressions;
                $this->videoDemandAdTagData[$demandAdTag->getId()][$this->getCacheKey(self::KEY_BIDS, $demandAdTagNameSpace)] = $demandAdTagBid;
                $this->videoDemandAdTagData[$demandAdTag->getId()][$this->getCacheKey(self::KEY_CLICKS, $demandAdTagNameSpace)] = mt_rand(0, $demandAdTagImpressions);
                $this->videoDemandAdTagData[$demandAdTag->getId()][$this->getCacheKey(self::KEY_ERRORS, $demandAdTagNameSpace)] = $demandAdTagRequest - $demandAdTagBid;
                $this->videoDemandAdTagData[$demandAdTag->getId()][$this->getCacheKey(self::KEY_BLOCKS, $demandAdTagNameSpace)] = $demandAdTagBlocks;
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function getVideoWaterfallTagData($videoWaterfallTagId, $supportMGet = true, $date = null)
    {
        // TODO: Implement getVideoWaterfallTagData() method.
    }

    /**
     * @inheritdoc
     */
    public function getVideoDemandAdTagData($videoDemandAdTagId, $supportMGet = true, $date = null)
    {
        if ($date instanceof DateTime) {
            $this->setDate($date);
        }

        if (isset($this->videoDemandAdTagData[$videoDemandAdTagId])) {
            return new VideoDemandAdTagReportData($videoDemandAdTagId, $this->videoDemandAdTagData[$videoDemandAdTagId], $this->getDate());
        }

        return new VideoDemandAdTagReportData($videoDemandAdTagId, [], $this->getDate());
    }

    /**
     * @inheritdoc
     */
    public function getVideoWaterfallTagRequestCount($videoWaterfallTagId, $date = null)
    {
        if ($date instanceof DateTime) {
            $this->setDate($date);
        }

        $adTagNameSpace = $this->getNamespace(self::NAMESPACE_WATERFALL_AD_TAG, $videoWaterfallTagId);
        $cacheKey = $this->getCacheKey(self::KEY_REQUESTS, $adTagNameSpace);
        if (array_key_exists($videoWaterfallTagId, $this->videoWaterfallTagData) && array_key_exists($cacheKey, $this->videoWaterfallTagData[$videoWaterfallTagId])) {
            return $this->videoWaterfallTagData[$videoWaterfallTagId][$cacheKey];
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function getVideoWaterfallTagBidCount($videoWaterfallTagId, $date = null)
    {
        if ($date instanceof DateTime) {
            $this->setDate($date);
        }

        $adTagNameSpace = $this->getNamespace(self::NAMESPACE_WATERFALL_AD_TAG, $videoWaterfallTagId);
        $cacheKey = $this->getCacheKey(self::KEY_BIDS, $adTagNameSpace);
        if (array_key_exists($videoWaterfallTagId, $this->videoWaterfallTagData) && array_key_exists($cacheKey, $this->videoWaterfallTagData[$videoWaterfallTagId])) {
            return $this->videoWaterfallTagData[$videoWaterfallTagId][$cacheKey];
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function getVideoWaterfallTagErrorCount($videoWaterfallTagId, $date = null)
    {
        if ($date instanceof DateTime) {
            $this->setDate($date);
        }

        $adTagNameSpace = $this->getNamespace(self::NAMESPACE_WATERFALL_AD_TAG, $videoWaterfallTagId);
        $cacheKey = $this->getCacheKey(self::KEY_ERRORS, $adTagNameSpace);
        if (array_key_exists($videoWaterfallTagId, $this->videoWaterfallTagData) && array_key_exists($cacheKey, $this->videoWaterfallTagData[$videoWaterfallTagId])) {
            return $this->videoWaterfallTagData[$videoWaterfallTagId][$cacheKey];
        }

        return false;
    }

    public function getVideoDemandAdTagImpressionsCount($videoDemandAdTagId, $date = null)
    {
        if ($date instanceof DateTime) {
            $this->setDate($date);
        }

        $adTagNameSpace = $this->getNamespace(self::NAMESPACE_DEMAND_AD_TAG, $videoDemandAdTagId);
        $cacheKey = $this->getCacheKey(self::KEY_IMPRESSIONS, $adTagNameSpace);
        if (array_key_exists($videoDemandAdTagId, $this->videoDemandAdTagData) && array_key_exists($cacheKey, $this->videoDemandAdTagData[$videoDemandAdTagId])) {
            return $this->videoDemandAdTagData[$videoDemandAdTagId][$cacheKey];
        }

        return false;
    }

    public function getVideoDemandAdTagRequestsCount($videoDemandAdTagId, $date = null)
    {
        if ($date instanceof DateTime) {
            $this->setDate($date);
        }

        $adTagNameSpace = $this->getNamespace(self::NAMESPACE_DEMAND_AD_TAG, $videoDemandAdTagId);
        $cacheKey = $this->getCacheKey(self::KEY_REQUESTS, $adTagNameSpace);
        if (array_key_exists($videoDemandAdTagId, $this->videoDemandAdTagData) && array_key_exists($cacheKey, $this->videoDemandAdTagData[$videoDemandAdTagId])) {
            return $this->videoDemandAdTagData[$videoDemandAdTagId][$cacheKey];
        }

        return false;
    }


    /**
     * seed Random Generator
     */
    protected function seedRandomGenerator()
    {
        list($usec, $sec) = explode(' ', microtime());
        $seed = (float)$sec + ((float)$usec * 100000);

        mt_srand($seed);
    }

    /**
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

    /**
     * validate that an uuid string is an uuid version 4
     * the format of uuid version 4: xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx
     *
     * @param $uuid4
     * @return bool
     */
    private function isValidUuidV4($uuid4)
    {
        return (bool)preg_match('/[a-f0-9]{8}\-[a-f0-9]{4}\-4[a-f0-9]{3}\-(8|9|a|b)[a-f0-9]{3}\-[a-f0-9]{12}/', $uuid4, $m);
    }
}