<?php

// Used only for importing legacy data into the new report table format
// Not used for any other purpose

use Tagcade\Service\Report\PerformanceReport\Display\Counter\AbstractEventCounter;

class PdoEventCounter extends AbstractEventCounter
{
    const DB_DATE = 'Y-m-d';

    /**
     * @var PDO
     */
    private $dbh;

    public function __construct(PDO $dbh)
    {
        $this->dbh = $dbh;
    }

    /**
     * @inheritdoc
     */
    public function getSlotOpportunityCount($slotId)
    {
        if (!is_numeric($slotId)) {
            return false;
        }

        $sth = $this->dbh->prepare("SELECT opportunities FROM reports_ad_slots where date = ? and ad_slot_id = ?");
        $sth->execute([$this->getDbDate(), $slotId]);

        return $sth->fetchColumn();
    }

    /**
     * @inheritdoc
     */
    public function getOpportunityCount($tagId)
    {
        if (!is_numeric($tagId)) {
            return false;
        }

        $sth = $this->dbh->prepare("SELECT opportunities FROM reports_ad_tags where date = ? and ad_tag_id = ?");
        $sth->execute([$this->getDbDate(), $tagId]);

        return $sth->fetchColumn();
    }

    /**
     * @inheritdoc
     */
    public function getImpressionCount($tagId)
    {
        if (!is_numeric($tagId)) {
            return false;
        }

        $sth = $this->dbh->prepare("SELECT impressions FROM reports_ad_tags where date = ? and ad_tag_id = ?");
        $sth->execute([$this->getDbDate(), $tagId]);

        return $sth->fetchColumn();
    }

    /**
     * @inheritdoc
     */
    public function getPassbackCount($tagId)
    {
        if (!is_numeric($tagId)) {
            return false;
        }

        $sth = $this->dbh->prepare("SELECT fallback_impressions FROM reports_ad_tags where date = ? and ad_tag_id = ?");
        $sth->execute([$this->getDbDate(), $tagId]);

        return $sth->fetchColumn();
    }

    protected function getDbDate() {
        return $this->date->format(self::DB_DATE);
    }

    /**
     * @inheritdoc
     */
    public function getHeaderBidRequestCount($slotId)
    {
        // TODO: Implement getHeaderBidRequestCount() method.
    }

    /**
     * @inheritdoc
     */
    public function getInBannerRequestCount($slotId)
    {
        // TODO: Implement getInBannerRequestCount() method.
    }

    /**
     * @inheritdoc
     */
    public function getAccountInBannerRequestCount($publisherId)
    {
        // TODO: Implement getAccountInBannerRequestCount() method.
    }

    /**
     * @inheritdoc
     */
    public function getRonInBannerRequestCount($slotId, $segment = null)
    {
        // TODO: Implement getRonInBannerRequestCount() method.
    }

    /**
     * @inheritdoc
     */
    public function getInBannerImpressionCount($slotId)
    {
        // TODO: Implement getInBannerImpressionCount() method.
    }

    /**
     * @inheritdoc
     */
    public function getAccountInBannerImpressionCount($publisherId)
    {
        // TODO: Implement getAccountInBannerImpressionCount() method.
    }

    /**
     * @inheritdoc
     */
    public function getRonInBannerImpressionCount($slotId, $segment = null)
    {
        // TODO: Implement getRonInBannerImpressionCount() method.
    }

    /**
     * @inheritdoc
     */
    public function getInBannerTimeoutCount($slotId)
    {
        // TODO: Implement getInBannerTimeoutCount() method.
    }

    /**
     * @inheritdoc
     */
    public function getAccountInBannerTimeoutCount($publisherId)
    {
        // TODO: Implement getAccountInBannerTimeoutCount() method.
    }

    /**
     * @inheritdoc
     */
    public function getRonInBannerTimeoutCount($slotId, $segment = null)
    {
        // TODO: Implement getRonInBannerTimeoutCount() method.
    }

    /**
     * @inheritdoc
     */
    public function getRonSlotOpportunityCount($ronSlotId, $segment = null)
    {
        // TODO: Implement getRonSlotOpportunityCount() method.
    }

    /**
     * @inheritdoc
     */
    public function getRonOpportunityCount($ronTagId, $segment = null)
    {
        // TODO: Implement getRonOpportunityCount() method.
    }

    /**
     * @inheritdoc
     */
    public function getRonImpressionCount($ronTagId, $segment = null)
    {
        // TODO: Implement getRonImpressionCount() method.
    }

    /**
     * @inheritdoc
     */
    public function getRonPassbackCount($ronTagId, $segment = null)
    {
        // TODO: Implement getRonPassbackCount() method.
    }

    /**
     * @inheritdoc
     */
    public function getFirstOpportunityCount($tagId)
    {
        // TODO: Implement getFirstOpportunityCount() method.
    }

    /**
     * @inheritdoc
     */
    public function getRonFirstOpportunityCount($ronTagId, $segment = null)
    {
        // TODO: Implement getRonFirstOpportunityCount() method.
    }

    /**
     * @inheritdoc
     */
    public function getVerifiedImpressionCount($tagId)
    {
        // TODO: Implement getVerifiedImpressionCount() method.
    }

    /**
     * @inheritdoc
     */
    public function getRonVerifiedImpressionCount($ronTagId, $segment = null)
    {
        // TODO: Implement getRonVerifiedImpressionCount() method.
    }

    /**
     * @inheritdoc
     */
    public function getUnverifiedImpressionCount($tagId)
    {
        // TODO: Implement getUnverifiedImpressionCount() method.
    }

    /**
     * @inheritdoc
     */
    public function getRonUnverifiedImpressionCount($ronTagId, $segment = null)
    {
        // TODO: Implement getRonUnverifiedImpressionCount() method.
    }

    /**
     * @inheritdoc
     */
    public function getBlankImpressionCount($tagId)
    {
        // TODO: Implement getBlankImpressionCount() method.
    }

    /**
     * @inheritdoc
     */
    public function getRonBlankImpressionCount($ronTagId, $segment = null)
    {
        // TODO: Implement getRonBlankImpressionCount() method.
    }

    /**
     * @inheritdoc
     */
    public function getVoidImpressionCount($tagId)
    {
        // TODO: Implement getVoidImpressionCount() method.
    }

    /**
     * @inheritdoc
     */
    public function getRonVoidImpressionCount($ronTagId, $segment = null)
    {
        // TODO: Implement getRonVoidImpressionCount() method.
    }

    /**
     * @inheritdoc
     */
    public function getClickCount($tagId)
    {
        // TODO: Implement getClickCount() method.
    }

    /**
     * @inheritdoc
     */
    public function getRonClickCount($ronTagId, $segment = null)
    {
        // TODO: Implement getRonClickCount() method.
    }

    /**
     * @inheritdoc
     */
    public function getRefreshesCount($tagId)
    {
        // TODO: Implement getRefreshesCount() method.
    }

    /**
     * @inheritdoc
     */
    public function getAdSlotReports(array $adSlotIds)
    {
        // TODO: Implement getAdSlotReports() method.
    }

    /**
     * @inheritdoc
     */
    public function getAdSlotReport(\Tagcade\Model\Core\ReportableAdSlotInterface $slot)
    {
        // TODO: Implement getAdSlotReport() method.
    }

    /**
     * @inheritdoc
     */
    public function getAdTagReport($tagId, $nativeSlot = false)
    {
        // TODO: Implement getAdTagReport() method.
    }

    /**
     * @inheritdoc
     */
    public function getAdTagReports(array $tagIds, $nativeSlot = false)
    {
        // TODO: Implement getAdTagReports() method.
    }

    /**
     * @inheritdoc
     */
    public function getNetworkReport(array $tagIds, $nativeSlot = false)
    {
        // TODO: Implement getNetworkReport() method.
    }

    /**
     * @inheritdoc
     */
    public function getRonAdTagReport($ronTagId, $segmentId = null, $hasNativeSlotContainer = false)
    {
        // TODO: Implement getRonAdTagReport() method.
    }

    /**
     * @inheritdoc
     */
    public function getRonAdTagReports(array $tagIds, $segmentId = null, $nativeSlot = false)
    {
        // TODO: Implement getRonAdTagReports() method.
    }

    /**
     * @inheritdoc
     */
    public function getRonAdSlotReport($ronAdSlotId, $segmentId = null)
    {
        // TODO: Implement getRonAdSlotReport() method.
    }

    /**
     * @inheritdoc
     */
    public function getAccountReport(\Tagcade\Model\User\Role\PublisherInterface $publisher)
    {
        // TODO: Implement getAccountReport() method.
    }

    /**
     * @inheritdoc
     */
    public function getSiteReportData(\Tagcade\Model\Core\SiteInterface $site)
    {
        // TODO: Implement getSiteReportData() method.
    }

    /**
     * @inheritdoc
     */
    public function getAccountReports(array $publishers)
    {
        // TODO: Implement getAccountReports() method.
    }
}