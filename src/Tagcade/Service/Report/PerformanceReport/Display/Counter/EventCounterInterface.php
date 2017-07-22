<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Counter;

use DateTime;
use Tagcade\Domain\DTO\Report\Performance\AdTagReportCount;
use Tagcade\Model\Core\ReportableAdSlotInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface EventCounterInterface
{
    /**
     * @param DateTime|null $date
     * @return self
     */
    public function setDate(DateTime $date = null);

    /**
     * @return DateTime
     */
    public function getDate();

    /**
     * @param int $slotId
     * @return mixed
     */
    public function getSlotOpportunityCount($slotId);

    /**
     * @param $slotId
     * @return mixed
     */
    public function getHeaderBidRequestCount($slotId);

    /**
     * @param $slotId
     * @return mixed
     */
    public function getInBannerRequestCount($slotId);

    public function getAccountInBannerRequestCount($publisherId);

    /**
     * @param $slotId
     * @param $segment
     * @return mixed
     */
    public function getRonInBannerRequestCount($slotId, $segment = null);

    /**
     * @param $slotId
     * @return mixed
     */
    public function getInBannerImpressionCount($slotId);

    public function getAccountInBannerImpressionCount($publisherId);

    /**
     * @param $slotId
     * @param $segment = null
     * @return mixed
     */
    public function getRonInBannerImpressionCount($slotId, $segment = null);

    /**
     * @param $slotId
     * @return mixed
     */
    public function getInBannerTimeoutCount($slotId);

    public function getAccountInBannerTimeoutCount($publisherId);

    /**
     * @param $slotId
     * @param $segment = null
     * @return mixed
     */
    public function getRonInBannerTimeoutCount($slotId, $segment = null);

    /**
     * @param int $ronSlotId
     * @param int|null $segment
     * @return mixed
     */
    public function getRonSlotOpportunityCount($ronSlotId, $segment = null);

    /**
     * @param int $tagId
     * @return int|bool
     */
    public function getOpportunityCount($tagId);

    /**
     * @param int $ronTagId
     * @param int|null $segment
     * @return mixed
     */
    public function getRonOpportunityCount($ronTagId, $segment = null);


    /**
     * @param int $tagId
     * @return int|bool
     */
    public function getImpressionCount($tagId);

    /**
     * @param int $ronTagId
     * @param int|null $segment
     * @return mixed
     */
    public function getRonImpressionCount($ronTagId, $segment = null);

    /**
     * @param int $tagId
     * @return int|bool
     */
    public function getPassbackCount($tagId);

    /**
     * @param int $ronTagId
     * @param int|null $segment
     * @return mixed
     */
    public function getRonPassbackCount($ronTagId, $segment = null);

    /**
     * @param int $tagId
     * @return int|bool
     */
    public function getFirstOpportunityCount($tagId);

    /**
     * @param int $ronTagId
     * @param int|null $segment
     * @return mixed
     */
    public function getRonFirstOpportunityCount($ronTagId, $segment = null);

    /**
     * @param int $tagId
     * @return int|bool
     */
    public function getVerifiedImpressionCount($tagId);

    /**
     * @param int $ronTagId
     * @param int|null $segment
     * @return mixed
     */
    public function getRonVerifiedImpressionCount($ronTagId, $segment = null);

    /**
     * @param int $tagId
     * @return int|bool
     */
    public function getUnverifiedImpressionCount($tagId);

    /**
     * @param int $ronTagId
     * @param int|null $segment
     * @return mixed
     */
    public function getRonUnverifiedImpressionCount($ronTagId, $segment = null);

    /**
     * @param int $tagId
     * @return int|bool
     */
    public function getBlankImpressionCount($tagId);

    /**
     * @param int $ronTagId
     * @param int|null $segment
     * @return mixed
     */
    public function getRonBlankImpressionCount($ronTagId, $segment = null);

    /**
     * @param int $tagId
     * @return int|bool
     */
    public function getVoidImpressionCount($tagId);

    /**
     * @param int $ronTagId
     * @param int|null $segment
     * @return mixed
     */
    public function getRonVoidImpressionCount($ronTagId, $segment = null);

    /**
     * @param int $tagId
     * @return int|bool
     */
    public function getClickCount($tagId);

    /**
     * @param int $ronTagId
     * @param int|null $segment
     * @return mixed
     */
    public function getRonClickCount($ronTagId, $segment = null);

    /**
     *
     * @param array $adSlotIds
     *
     * @return array('slotId' => AdSlotReportCount)
     */
    public function getAdSlotReports(array $adSlotIds);

    /**
     * @param ReportableAdSlotInterface $slot
     * @return array
     */
    public function getAdSlotReport(ReportableAdSlotInterface $slot);

    /**
     * @param $tagId
     * @param bool $nativeSlot whether ad slot containing this tag is native or not
     *
     * @return AdTagReportCount
     */
    public function getAdTagReport($tagId, $nativeSlot = false);

    /**
     * Get reports for a list of ad tags
     *
     * @param array $tagIds
     * @param bool $nativeSlot  whether ad slot containing these tags is native or not
     *
     * @return array('tagId' => AdTagReportCount)
     */
    public function getAdTagReports(array $tagIds, $nativeSlot = false);

    public function getNetworkReport(array $tagIds, $nativeSlot = false);

    public function getRonAdTagReport($ronTagId, $segmentId = null, $hasNativeSlotContainer = false);

    public function getRonAdTagReports(array $tagIds, $segmentId = null, $nativeSlot = false);

    public function getRonAdSlotReport($ronAdSlotId, $segmentId = null);

    /**
     * get account report
     *
     * @param PublisherInterface $publisher
     *
     * @return array
     */
    public function getAccountReport(PublisherInterface $publisher);

    /**
     * @param SiteInterface $site
     * @return mixed
     */
    public function getSiteReportData(SiteInterface $site);

    /**
     * get account reports
     *
     * @param array|PublisherInterface[] $publishers
     *
     * @return AdTagReportCount
     */
    public function getAccountReports(array $publishers);
}