<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Counter;


use Tagcade\Domain\DTO\Report\Performance\AccountReportCount;
use Tagcade\Domain\DTO\Report\Performance\AdSlotReportCount;
use Tagcade\Domain\DTO\Report\Performance\AdTagReportCount;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\DisplayAdSlotInterface;
use Tagcade\Model\Core\DynamicAdSlotInterface;
use Tagcade\Model\Core\LibrarySlotTagInterface;
use Tagcade\Model\Core\ReportableAdSlotInterface;
use Tagcade\Model\Core\RonAdSlotInterface;
use Tagcade\Model\Core\SegmentInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;

/**
 * This counter is only used for testing
 */

class TestEventCounter extends AbstractEventCounter
{
    const CACHE_KEY_ACC_SLOT_OPPORTUNITY   = 'slot_opportunities';
    const CACHE_KEY_ACC_OPPORTUNITY       = 'opportunities';

    const KEY_OPPORTUNITY            = 'opportunities';
    const KEY_SLOT_OPPORTUNITY       = 'opportunities';
    const KEY_IMPRESSION             = 'impressions';
    const KEY_RTB_IMPRESSION         = 'impression';
    const KEY_HB_BID_REQUEST         = 'hb_bid_request';
    const KEY_PASSBACK               = 'passbacks';
    const KEY_FIRST_OPPORTUNITY      = 'first_opportunities';
    const KEY_VERIFIED_IMPRESSION    = 'verified_impressions';
    const KEY_UNVERIFIED_IMPRESSION  = 'unverified_impressions';
    const KEY_BLANK_IMPRESSION       = 'blank_impressions';
    const KEY_VOID_IMPRESSION        = 'void_impressions';
    const KEY_CLICK                  = 'clicks';

    const KEY_IN_BANNER_REQUESTS     = 'requests';
    const KEY_IN_BANNER_IMPRESSIONS  = 'impressions';
    const KEY_IN_BANNER_TIMEOUT      = 'timeouts';

    protected $adSlots;
    protected $adSlotData = [];
    protected $adTagData = [];
    protected $ronAdSlotData = [];
    protected $ronAdSlotSegmentData = [];
    protected $ronAdTagData = [];
    protected $ronAdTagSegmentData = [];

    protected $accountData = [];

    /**
     * @param ReportableAdSlotInterface[] $adSlots
     */
    public function __construct(array $adSlots)
    {
        $this->adSlots = $adSlots;
    }

    public function refreshTestData($minSlotOpportunities = 10000, $maxSlotOpportunities = 100000)
    {
        $this->adSlotData = [];
        $this->adTagData = [];
        $this->ronAdSlotData = [];
        $this->ronAdSlotSegmentData = [];
        $this->ronAdTagData = [];
        $this->ronAdTagSegmentData = [];

        $this->accountData = [];

        /**
         * @var BaseAdSlotInterface $adSlot
         */
        foreach($this->adSlots as $adSlot) {
            $this->seedRandomGenerator();

            $slotOpportunities = mt_rand($minSlotOpportunities, $maxSlotOpportunities);
            $rtbImpressions = mt_rand(0, $slotOpportunities * 0.01);
            $inBannerRequests = mt_rand(0, $slotOpportunities * 0.02);
            $inBannerImpressions = mt_rand(0, $inBannerRequests);
            $hbRequests = mt_rand(0, $rtbImpressions);
            $opportunitiesRemaining = $slotOpportunities - $rtbImpressions;

            $publisherId = $adSlot->getSite()->getPublisherId();
            if (!isset($this->accountData[$publisherId])) {
                $this->accountData[$publisherId] = array(
                    static::CACHE_KEY_ACC_SLOT_OPPORTUNITY => 0,
                    static::CACHE_KEY_ACC_OPPORTUNITY => 0,
                    static::KEY_RTB_IMPRESSION => 0,
                    static::KEY_IN_BANNER_REQUESTS => 0,
                    static::KEY_IN_BANNER_TIMEOUT => 0,
                    static::KEY_IN_BANNER_IMPRESSIONS => 0,
                    static::KEY_HB_BID_REQUEST => 0,
                    static::KEY_PASSBACK => 0,
                );
            }

            if (!$adSlot instanceof DynamicAdSlotInterface) {
                $this->adSlotData[$adSlot->getId()] = [
                    static::KEY_SLOT_OPPORTUNITY => $slotOpportunities,
                    static::KEY_RTB_IMPRESSION => $rtbImpressions
                ];

                $this->accountData[$publisherId][static::CACHE_KEY_ACC_SLOT_OPPORTUNITY] += $slotOpportunities;
                $this->accountData[$publisherId][static::KEY_RTB_IMPRESSION] += $rtbImpressions;
            }

            if ($adSlot instanceof DisplayAdSlotInterface && $adSlot->getSite()->getPublisher()->hasInBannerModule()) {
                $this->adSlotData[$adSlot->getId()][static::KEY_IN_BANNER_REQUESTS] = $inBannerRequests;
                $this->adSlotData[$adSlot->getId()][static::KEY_IN_BANNER_IMPRESSIONS] = $inBannerImpressions;
                $this->adSlotData[$adSlot->getId()][static::KEY_IN_BANNER_TIMEOUT] = $inBannerRequests - $inBannerImpressions;

                $this->accountData[$publisherId][static::KEY_IN_BANNER_REQUESTS] += $inBannerRequests;
                $this->accountData[$publisherId][static::KEY_IN_BANNER_IMPRESSIONS] += $inBannerImpressions;
                $this->accountData[$publisherId][static::KEY_IN_BANNER_TIMEOUT] += $this->adSlotData[$adSlot->getId()][static::KEY_IN_BANNER_TIMEOUT];
            }

            if($adSlot->getSite()->getPublisher()->hasHeaderBiddingModule()) {
                $this->adSlotData[$adSlot->getId()][static::KEY_HB_BID_REQUEST] = $hbRequests;
                $this->accountData[$publisherId][static::KEY_HB_BID_REQUEST] += $hbRequests;
            }

            $ronAdSlot = $adSlot->getLibraryAdSlot()->getRonAdSlot();
            if ($ronAdSlot instanceof RonAdSlotInterface) {
                $currentData = array_key_exists($ronAdSlot->getId(), $this->ronAdSlotData) ? $this->ronAdSlotData[$ronAdSlot->getId()] : null;

                $this->ronAdSlotData[$ronAdSlot->getId()] = $this->arraySum([
                    static::KEY_SLOT_OPPORTUNITY => $slotOpportunities,
                    static::KEY_RTB_IMPRESSION => $rtbImpressions,
                ], $currentData);

                $totalRonSlotOpportunities = $this->ronAdSlotData[$ronAdSlot->getId()][static::KEY_SLOT_OPPORTUNITY];
                $segmentCount = count($ronAdSlot->getSegments());
                $slotSegments = $this->distributeValueToArray($totalRonSlotOpportunities, $segmentCount);
                $slotSegmentRtbImpressions = $this->distributeValueToArray($rtbImpressions, $segmentCount);
                foreach ($ronAdSlot->getSegments() as $index => $segment) {
                    /**
                     * @var SegmentInterface $segment
                     */
                    $this->ronAdSlotSegmentData[$ronAdSlot->getId()][$segment->getId()] =  [
                        static::KEY_SLOT_OPPORTUNITY => $slotSegments[$index],
                        static::KEY_RTB_IMPRESSION => $slotSegmentRtbImpressions[$index],
                    ];
                }
            }

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
                $voidImpressions = mt_rand(0, $verifiedImpressions);
                $blankImpressions = (int)($verifiedImpressions - $voidImpressions);
                $unverifiedImpressions = $impressions - $verifiedImpressions;
                $clicks = mt_rand(0, $verifiedImpressions);

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
                    static::KEY_VOID_IMPRESSION => $voidImpressions,
                    static::KEY_CLICK => $clicks,
                ];

                $this->accountData[$publisherId][static::CACHE_KEY_ACC_OPPORTUNITY] += $opportunities;
                $this->accountData[$publisherId][static::KEY_IMPRESSION] += $impressions;
                $this->accountData[$publisherId][static::KEY_PASSBACK] += $passbacks;

                $opportunitiesRemaining = $passbacks;

                $libSlotTags = $adTag->getLibraryAdTag()->getLibSlotTags();
                foreach ($libSlotTags as $slotTag) {
                    /**
                     * @var LibrarySlotTagInterface $slotTag
                     */

                    $ronSlot = $slotTag->getLibraryAdSlot()->getRonAdSlot();
                    if ($ronSlot instanceof RonAdSlotInterface) {
                        $currentData = array_key_exists($slotTag->getId(), $this->ronAdTagData) ? $this->ronAdTagData[$slotTag->getId()] : null;

                        $this->ronAdTagData[$slotTag->getId()] = $this->arraySum([
                            static::KEY_OPPORTUNITY => $opportunities,
                            static::KEY_IMPRESSION => $impressions,
                            static::KEY_PASSBACK => $passbacks,
                            static::KEY_FIRST_OPPORTUNITY => $firstOpportunities,
                            static::KEY_VERIFIED_IMPRESSION => $verifiedImpressions,
                            static::KEY_UNVERIFIED_IMPRESSION => $unverifiedImpressions,
                            static::KEY_BLANK_IMPRESSION => $blankImpressions,
                            static::KEY_VOID_IMPRESSION => $voidImpressions,
                            static::KEY_CLICK => $clicks,
                        ], $currentData);

                        // distribute events to different segments
                        $segmentCount = count($ronSlot->getSegments());
                        $tmpOppArrVal = $this->distributeValueToArray($this->ronAdTagData[$slotTag->getId()][static::KEY_OPPORTUNITY], $segmentCount);
                        $tmpImpArrVal = $this->distributeValueToArray($this->ronAdTagData[$slotTag->getId()][static::KEY_IMPRESSION], $segmentCount);
                        $tmpPassbackArrVal = $this->distributeValueToArray($this->ronAdTagData[$slotTag->getId()][static::KEY_PASSBACK], $segmentCount);
                        $tmpFirstOppArrVal = $this->distributeValueToArray($this->ronAdTagData[$slotTag->getId()][static::KEY_FIRST_OPPORTUNITY], $segmentCount);
                        $tmpVerImpArrVal = $this->distributeValueToArray($this->ronAdTagData[$slotTag->getId()][static::KEY_VERIFIED_IMPRESSION], $segmentCount);
                        $tmpUnverImpArrVal = $this->distributeValueToArray($this->ronAdTagData[$slotTag->getId()][static::KEY_UNVERIFIED_IMPRESSION], $segmentCount);
                        $tmpBlankImpArrVal = $this->distributeValueToArray($this->ronAdTagData[$slotTag->getId()][static::KEY_BLANK_IMPRESSION], $segmentCount);
                        $tmpVoidImpArrVal = $this->distributeValueToArray($this->ronAdTagData[$slotTag->getId()][static::KEY_VOID_IMPRESSION], $segmentCount);
                        $tmpClickArrVal = $this->distributeValueToArray($this->ronAdTagData[$slotTag->getId()][static::KEY_CLICK], $segmentCount);

                        $i = 0;
                        foreach ($ronSlot->getSegments() as $segment) {
                            /**
                             * @var SegmentInterface $segment
                             */
                            $this->ronAdTagSegmentData[$slotTag->getId()][$segment->getId()] =  [
                                static::KEY_SLOT_OPPORTUNITY => $tmpOppArrVal[$i],
                                static::KEY_IMPRESSION => $tmpImpArrVal[$i],
                                static::KEY_PASSBACK => $tmpPassbackArrVal[$i],
                                static::KEY_FIRST_OPPORTUNITY => $tmpFirstOppArrVal[$i],
                                static::KEY_VERIFIED_IMPRESSION => $tmpVerImpArrVal[$i],
                                static::KEY_UNVERIFIED_IMPRESSION => $tmpUnverImpArrVal[$i],
                                static::KEY_BLANK_IMPRESSION => $tmpBlankImpArrVal[$i],
                                static::KEY_VOID_IMPRESSION => $tmpVoidImpArrVal[$i],
                                static::KEY_CLICK => $tmpClickArrVal[$i],
                            ];
                            $i ++;
                        }

                    }
                }
            }
        }
    }

    private function arraySum(array $array1, array $array2 = null) {
        if (null === $array2) {
            return $array1;
        }

        $arrayFinal = array();
        foreach ($array1 as $key => $val) {
            if (!array_key_exists($key, $array2)) {
                throw new RuntimeException(sprintf('expect key "%s" in both array', $key));
            }

            $arrayFinal[$key] = $array1[$key] + $array2[$key];
        }

        return $arrayFinal;
    }

    /**
     * @param $value
     * @param $arraySize
     * @return array that has size = $arraySize
     */
    private function distributeValueToArray($value, $arraySize) {

        if (!is_int($arraySize) || $arraySize < 0) {
            throw new InvalidArgumentException('expect a positive array size');
        }

        if ($arraySize < 2) {
            return array($value);
        }

        $maxEachItem = floor(100 / $arraySize);

        $result = [];
        for($i = 0; $i < $arraySize - 1; $i ++) {
            $tmpVal = mt_rand(0, $maxEachItem);
            $result[] = round($tmpVal * $value / 100);
        }

        $currentTotal = array_sum($result);
        $result[] = $value - $currentTotal;

        return $result;
    }

    public function getAdSlotData()
    {
        return $this->adSlotData;
    }

    public function getAccountData()
    {
        return $this->accountData;
    }

    public function getAdTagData()
    {
        return $this->adTagData;
    }

    /**
     * @return array
     */
    public function getRonAdSlotData()
    {
        return $this->ronAdSlotData;
    }

    /**
     * @return array
     */
    public function getRonAdTagData()
    {
        return $this->ronAdTagData;
    }

    /**
     * @return array
     */
    public function getRonAdSlotSegmentData()
    {
        return $this->ronAdSlotSegmentData;
    }

    /**
     * @return array
     */
    public function getRonAdTagSegmentData()
    {
        return $this->ronAdTagSegmentData;
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

    public function getRtbImpressionsCount($slotId)
    {
        if (!isset($this->adSlotData[$slotId][static::KEY_RTB_IMPRESSION])) {
            return false;
        }

        return $this->adSlotData[$slotId][static::KEY_RTB_IMPRESSION];
    }

    public function getAccountRtbImpressionsCount($publisherId)
    {
        if (!isset($this->accountData[$publisherId][static::KEY_RTB_IMPRESSION])) {
            return false;
        }

        return $this->accountData[$publisherId][static::KEY_RTB_IMPRESSION];
    }


    public function getHeaderBidRequestCount($slotId)
    {
        if (!isset($this->adSlotData[$slotId][static::KEY_HB_BID_REQUEST])) {
            return false;
        }

        return $this->adSlotData[$slotId][static::KEY_HB_BID_REQUEST];
    }

    public function getInBannerRequestCount($slotId)
    {
        if (!isset($this->adSlotData[$slotId][static::KEY_IN_BANNER_REQUESTS])) {
            return false;
        }

        return $this->adSlotData[$slotId][static::KEY_IN_BANNER_REQUESTS];
    }

    public function getAccountInBannerRequestCount($publisherId)
    {
        if (!isset($this->accountData[$publisherId][static::KEY_IN_BANNER_REQUESTS])) {
            return false;
        }

        return $this->accountData[$publisherId][static::KEY_IN_BANNER_REQUESTS];
    }


    public function getInBannerImpressionCount($slotId)
    {
        if (!isset($this->adSlotData[$slotId][static::KEY_IN_BANNER_IMPRESSIONS])) {
            return false;
        }

        return $this->adSlotData[$slotId][static::KEY_IN_BANNER_IMPRESSIONS];
    }

    public function getAccountInBannerImpressionCount($publisherId)
    {
        if (!isset($this->accountData[$publisherId][static::KEY_IN_BANNER_IMPRESSIONS])) {
            return false;
        }

        return $this->accountData[$publisherId][static::KEY_IN_BANNER_IMPRESSIONS];
    }


    public function getInBannerTimeoutCount($slotId)
    {
        if (!isset($this->adSlotData[$slotId][static::KEY_IN_BANNER_TIMEOUT])) {
            return false;
        }

        return $this->adSlotData[$slotId][static::KEY_IN_BANNER_TIMEOUT];
    }

    public function getAccountInBannerTimeoutCount($publisherId)
    {
        if (!isset($this->accountData[$publisherId][static::KEY_IN_BANNER_TIMEOUT])) {
            return false;
        }

        return $this->accountData[$publisherId][static::KEY_IN_BANNER_TIMEOUT];
    }


    public function getRonInBannerRequestCount($slotId, $segment = null)
    {
        if (!isset($this->ronAdSlotData[$slotId][static::KEY_IN_BANNER_REQUESTS])) {
            return false;
        }

        if (null !== $segment && !isset($this->ronAdSlotSegmentData[$slotId][$segment][static::KEY_IN_BANNER_REQUESTS] )) {
            return false;
        }

        return null !== $segment ? $this->ronAdSlotSegmentData[$slotId][$segment][static::KEY_IN_BANNER_REQUESTS] : $this->ronAdSlotData[$slotId][static::KEY_IN_BANNER_REQUESTS];
    }

    public function getRonInBannerImpressionCount($slotId, $segment = null)
    {
        if (!isset($this->ronAdSlotData[$slotId][static::KEY_IN_BANNER_IMPRESSIONS])) {
            return false;
        }

        if (null !== $segment && !isset($this->ronAdSlotSegmentData[$slotId][$segment][static::KEY_IN_BANNER_IMPRESSIONS] )) {
            return false;
        }

        return null !== $segment ? $this->ronAdSlotSegmentData[$slotId][$segment][static::KEY_IN_BANNER_IMPRESSIONS] : $this->ronAdSlotData[$slotId][static::KEY_IN_BANNER_IMPRESSIONS];
    }

    public function getRonInBannerTimeoutCount($slotId, $segment = null)
    {
        if (!isset($this->ronAdSlotData[$slotId][static::KEY_IN_BANNER_TIMEOUT])) {
            return false;
        }

        if (null !== $segment && !isset($this->ronAdSlotSegmentData[$slotId][$segment][static::KEY_IN_BANNER_TIMEOUT] )) {
            return false;
        }

        return null !== $segment ? $this->ronAdSlotSegmentData[$slotId][$segment][static::KEY_IN_BANNER_TIMEOUT] : $this->ronAdSlotData[$slotId][static::KEY_IN_BANNER_TIMEOUT];
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

    /**
     * @param int $tagId
     * @return int|bool
     */
    public function getVoidImpressionCount($tagId)
    {
        if (!isset($this->adTagData[$tagId][static::KEY_VOID_IMPRESSION])) {
            return false;
        }

        return $this->adTagData[$tagId][static::KEY_VOID_IMPRESSION];
    }

    /**
     * @param int $tagId
     * @return int|bool
     */
    public function getClickCount($tagId)
    {
        if (!isset($this->adTagData[$tagId][static::KEY_CLICK])) {
            return false;
        }

        return $this->adTagData[$tagId][static::KEY_CLICK];
    }

    /**
     * @param int $ronSlotId
     * @param int|null $segment
     * @return mixed
     */
    public function getRonSlotOpportunityCount($ronSlotId, $segment = null)
    {
        if (!isset($this->ronAdSlotData[$ronSlotId][static::KEY_SLOT_OPPORTUNITY])) {
            return false;
        }

        if (null !== $segment && !isset($this->ronAdSlotSegmentData[$ronSlotId][$segment][static::KEY_SLOT_OPPORTUNITY] )) {
            return false;
        }

        return null !== $segment ? $this->ronAdSlotSegmentData[$ronSlotId][$segment][static::KEY_SLOT_OPPORTUNITY] : $this->ronAdSlotData[$ronSlotId][static::KEY_SLOT_OPPORTUNITY];
    }

    public function getRonSlotRtbImpressionsCount($ronSlotId, $segment = null)
    {
        if (!isset($this->ronAdSlotData[$ronSlotId][static::KEY_RTB_IMPRESSION])) {
            return false;
        }

        if (null !== $segment && !isset($this->ronAdSlotSegmentData[$ronSlotId][$segment][static::KEY_RTB_IMPRESSION] )) {
            return false;
        }

        return null !== $segment ? $this->ronAdSlotSegmentData[$ronSlotId][$segment][static::KEY_RTB_IMPRESSION] : $this->ronAdSlotData[$ronSlotId][static::KEY_RTB_IMPRESSION];
    }


    /**
     * @param int $ronTagId
     * @param int|null $segment
     * @return mixed
     */
    public function getRonOpportunityCount($ronTagId, $segment = null)
    {
        if (!isset($this->ronAdTagData[$ronTagId][static::KEY_OPPORTUNITY])) {
            return false;
        }

        if (null !== $segment && !isset($this->ronAdTagSegmentData[$ronTagId][$segment][static::KEY_OPPORTUNITY] )) {
            return false;
        }

        return null !== $segment ? $this->ronAdTagSegmentData[$ronTagId][$segment][static::KEY_OPPORTUNITY] : $this->ronAdTagData[$ronTagId][static::KEY_OPPORTUNITY];
    }

    /**
     * @param int $ronTagId
     * @param int|null $segment
     * @return mixed
     */
    public function getRonImpressionCount($ronTagId, $segment = null)
    {
        if (!isset($this->ronAdTagData[$ronTagId][static::KEY_IMPRESSION])) {
            return false;
        }

        if (null !== $segment && !isset($this->ronAdTagSegmentData[$ronTagId][$segment][static::KEY_IMPRESSION] )) {
            return false;
        }

        return null !== $segment ? $this->ronAdTagSegmentData[$ronTagId][$segment][static::KEY_IMPRESSION] : $this->ronAdTagData[$ronTagId][static::KEY_IMPRESSION];
    }

    /**
     * @param int $ronTagId
     * @param int|null $segment
     * @return mixed
     */
    public function getRonPassbackCount($ronTagId, $segment = null)
    {
        if (!isset($this->ronAdTagData[$ronTagId][static::KEY_PASSBACK])) {
            return false;
        }

        if (null !== $segment && !isset($this->ronAdTagSegmentData[$ronTagId][$segment][static::KEY_PASSBACK] )) {
            return false;
        }

        return null !== $segment ? $this->ronAdTagSegmentData[$ronTagId][$segment][static::KEY_PASSBACK] : $this->ronAdTagData[$ronTagId][static::KEY_PASSBACK];
    }

    /**
     * @param int $ronTagId
     * @param int|null $segment
     * @return mixed
     */
    public function getRonFirstOpportunityCount($ronTagId, $segment = null)
    {
        if (!isset($this->ronAdTagData[$ronTagId][static::KEY_FIRST_OPPORTUNITY])) {
            return false;
        }

        if (null !== $segment && !isset($this->ronAdTagSegmentData[$ronTagId][$segment][static::KEY_FIRST_OPPORTUNITY] )) {
            return false;
        }

        return null !== $segment ? $this->ronAdTagSegmentData[$ronTagId][$segment][static::KEY_FIRST_OPPORTUNITY] : $this->ronAdTagData[$ronTagId][static::KEY_FIRST_OPPORTUNITY];
    }

    /**
     * @param int $ronTagId
     * @param int|null $segment
     * @return mixed
     */
    public function getRonVerifiedImpressionCount($ronTagId, $segment = null)
    {
        if (!isset($this->ronAdTagData[$ronTagId][static::KEY_VERIFIED_IMPRESSION])) {
            return false;
        }

        if (null !== $segment && !isset($this->ronAdTagSegmentData[$ronTagId][$segment][static::KEY_VERIFIED_IMPRESSION] )) {
            return false;
        }

        return null !== $segment ? $this->ronAdTagSegmentData[$ronTagId][$segment][static::KEY_VERIFIED_IMPRESSION] : $this->ronAdTagData[$ronTagId][static::KEY_VERIFIED_IMPRESSION];
    }

    /**
     * @param int $ronTagId
     * @param int|null $segment
     * @return mixed
     */
    public function getRonUnverifiedImpressionCount($ronTagId, $segment = null)
    {
        if (!isset($this->ronAdTagData[$ronTagId][static::KEY_UNVERIFIED_IMPRESSION])) {
            return false;
        }

        if (null !== $segment && !isset($this->ronAdTagSegmentData[$ronTagId][$segment][static::KEY_UNVERIFIED_IMPRESSION] )) {
            return false;
        }

        return null !== $segment ? $this->ronAdTagSegmentData[$ronTagId][$segment][static::KEY_UNVERIFIED_IMPRESSION] : $this->ronAdTagData[$ronTagId][static::KEY_UNVERIFIED_IMPRESSION];
    }

    /**
     * @param int $ronTagId
     * @param int|null $segment
     * @return mixed
     */
    public function getRonBlankImpressionCount($ronTagId, $segment = null)
    {
        if (!isset($this->ronAdTagData[$ronTagId][static::KEY_BLANK_IMPRESSION])) {
            return false;
        }

        if (null !== $segment && !isset($this->ronAdTagSegmentData[$ronTagId][$segment][static::KEY_BLANK_IMPRESSION] )) {
            return false;
        }

        return null !== $segment ? $this->ronAdTagSegmentData[$ronTagId][$segment][static::KEY_BLANK_IMPRESSION] : $this->ronAdTagData[$ronTagId][static::KEY_BLANK_IMPRESSION];
    }

    /**
     * @param int $ronTagId
     * @param int|null $segment
     * @return mixed
     */
    public function getRonVoidImpressionCount($ronTagId, $segment = null)
    {
        if (!isset($this->ronAdTagData[$ronTagId][static::KEY_VOID_IMPRESSION])) {
            return false;
        }

        if (null !== $segment && !isset($this->ronAdTagSegmentData[$ronTagId][$segment][static::KEY_VOID_IMPRESSION] )) {
            return false;
        }

        return null !== $segment ? $this->ronAdTagSegmentData[$ronTagId][$segment][static::KEY_VOID_IMPRESSION] : $this->ronAdTagData[$ronTagId][static::KEY_VOID_IMPRESSION];
    }

    /**
     * @param int $ronTagId
     * @param int|null $segment
     * @return mixed
     */
    public function getRonClickCount($ronTagId, $segment = null)
    {
        if (!isset($this->ronAdTagData[$ronTagId][static::KEY_CLICK])) {
            return false;
        }

        if (null !== $segment && !isset($this->ronAdTagSegmentData[$ronTagId][$segment][static::KEY_CLICK] )) {
            return false;
        }

        return null !== $segment ? $this->ronAdTagSegmentData[$ronTagId][$segment][static::KEY_CLICK] : $this->ronAdTagData[$ronTagId][static::KEY_CLICK];
    }


    protected function seedRandomGenerator()
    {
        list($usec, $sec) = explode(' ', microtime());
        $seed = (float) $sec + ((float) $usec * 100000);

        mt_srand($seed);
    }

    /**
     *
     * @param array $adSlotIds
     *
     * @return array('slotId' => AdSlotReportCount)
     */
    public function getAdSlotReports(array $adSlotIds)
    {
        $convertResults = [];
        foreach($adSlotIds as $adSlotId) {
            if (!is_int($adSlotId)) {
                throw new RuntimeException(sprintf('expect int value, %s given', get_class($adSlotId)));
            }

            $convertResults[] = new AdSlotReportCount($this->adSlotData[$adSlotId]);
        }

        return $convertResults;
    }

    /**
     * @param $tagId
     * @param bool $nativeSlot whether ad slot containing this tag is native or not
     *
     * @return AdTagReportCount
     */
    public function getAdTagReport($tagId, $nativeSlot = false)
    {
        if (!is_int($tagId)) {
            throw new RuntimeException(sprintf('expect int value, %s given', get_class($tagId)));
        }

        return new AdTagReportCount($this->adTagData[$tagId]);
    }

    /**
     * Get reports for a list of ad tags
     *
     * @param array $tagIds
     * @param bool $nativeSlot whether ad slot containing these tags is native or not
     *
     * @return array('tagId' => AdTagReportCount)
     */
    public function getAdTagReports(array $tagIds, $nativeSlot = false)
    {
        $convertedResults = [];
        foreach($tagIds as $tagId) {
            $convertedResults[] = $this->getAdTagReport($tagId);
        }

        return $convertedResults;
    }

    public function getRonAdTagReport($ronTagId, $segmentId = null, $hasNativeSlotContainer = false)
    {
        // TODO: Implement getRonAdTagReport() method.
    }

    public function getRonAdTagReports(array $tagIds, $segmentId = null, $nativeSlot = false)
    {
        // TODO: Implement getRonAdTagReports() method.
    }

    public function getRonAdSlotReport($ronAdSlotId, $segmentId = null)
    {
        // TODO: Implement getRonAdSlotReport() method.
    }

    /**
     * get account reports
     *
     * @param PublisherInterface $publisher
     * @return AdTagReportCount
     */
    public function getAccountReport(PublisherInterface $publisher)
    {
        return new AccountReportCount($this->accountData[$publisher->getId()]);
    }

    public function getAdSlotReport(ReportableAdSlotInterface $slot)
    {
        // TODO: Implement getAdSlotReport() method.
    }

    public function getNetworkReport(array $tagIds, $nativeSlot = false)
    {
        // TODO: Implement getNetworkReport() method.
    }

    public function getSiteReportData(SiteInterface $site)
    {
        // TODO: Implement getSiteReportData() method.
    }

    /**
     * Get reports for a list of ad tags
     *
     * @param array|PublisherInterface[] $publishers
     *
     * @return array('publisherId' => AccountReportCount)
     */
    public function getAccountReports(array $publishers)
    {
        $publisherIds = [];
        $convertedResults = [];

        foreach ($publishers as $publisher) {
            if ($publisher instanceof PublisherInterface) {
                $publisherIds[] = $publisher->getId();
            }
        }

        foreach($publisherIds as $publisherId) {
            $convertedResults[] = $this->getAccountReport($publisherId);
        }

        return $convertedResults;
    }
}