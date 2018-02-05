<?php

namespace Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform;

use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\DisplayAdSlotInterface;
use Tagcade\Model\Core\NativeAdSlotInterface;
use Tagcade\Model\Core\ReportableAdSlotInterface;
use Tagcade\Model\Report\CalculateEstProfitTrait;
use Tagcade\Model\Report\CalculateSupplyCostTrait;
use Tagcade\Model\Report\PerformanceReport\CalculateAdOpportunitiesTrait;
use Tagcade\Model\Report\PerformanceReport\Display\AbstractCalculatedReport as BaseAbstractCalculatedReport;
use Tagcade\Model\Report\PerformanceReport\Display\Fields\SuperReportTrait;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform\Fields\SlotOpportunitiesTrait;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;

/**
 * The Ad Slot report extends the common AbstractCalculatedReport but it modifies the doCalculateFields method
 * because its sub reports are the core ad tag reports which do not have separate total and slot opportunities
 * So the doCalculateFields method is custom for this type of report
 */
class AdSlotReport extends BaseAbstractCalculatedReport implements AdSlotReportInterface
{
    use SuperReportTrait;
    use SlotOpportunitiesTrait;
    use CalculateAdOpportunitiesTrait;
    use CalculateSupplyCostTrait;
    use CalculateEstProfitTrait;

    /**
     * @var BaseAdSlotInterface
     */
    protected $adSlot;

    /**
     * @var float
     */
    protected $customRate;

    /**
     * @var int
     */
    protected $refreshedSlotOpportunities; // special for ad slot only

    /**
     * @inheritdoc
     */
    public function getAdSlot()
    {
        return $this->adSlot;
    }

    /**
     * @inheritdoc
     */
    public function getAdSlotId()
    {
        if ($this->adSlot instanceof ReportableAdSlotInterface) {
            return $this->adSlot->getId();
        }

        return null;
    }

    protected function resetCounts()
    {
        if ($this->adSlot instanceof DisplayAdSlotInterface) {
            parent::resetCounts();

            return;
        }

        if (!$this->adSlot instanceof NativeAdSlotInterface) {
            return;
        }

        $this->inBannerTimeouts = 0;
        $this->inBannerRequests = 0;
        $this->inBannerImpressions = 0;
        $this->totalOpportunities = 0;
        $this->impressions = 0;
        $this->passbacks = null;
        $this->estRevenue = null;
    }

    /**
     * @param bool $chainToSubReports
     */
    public function setThresholdBilledAmount($chainToSubReports = true)
    {
        // We don't need to calculate threshold report here. The set billed amount is the threshold billed amount already
    }

    /**
     * @inheritdoc
     */
    public function getCustomRate()
    {
        return $this->customRate;
    }

    /**
     * @inheritdoc
     */
    public function setCustomRate($customRate)
    {
        $this->customRate = $customRate;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setAdSlot(BaseAdSlotInterface $adSlot)
    {
        $this->adSlot = $adSlot;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getRefreshedSlotOpportunities()
    {
        return $this->refreshedSlotOpportunities;
    }

    /**
     * @inheritdoc
     */
    public function setRefreshedSlotOpportunities($refreshedSlotOpportunities)
    {
        $this->refreshedSlotOpportunities = (int)$refreshedSlotOpportunities;
        return $this;
    }

    public function isValidSubReport(ReportInterface $report)
    {
        return $report instanceof AdTagReportInterface;
    }

    /**
     * @param ReportInterface $report
     * @return bool
     */
    public function isValidSuperReport(ReportInterface $report)
    {
        return $report instanceof SiteReportInterface;
    }

    /**
     * Overwrite the parent doCalculateFields
     *
     * This is because the sub reports are not calculated and contain the base values
     *
     * @throws \Tagcade\Exception\RuntimeException
     */
    protected function doCalculateFields()
    {
        if ($this->slotOpportunities === null) {
            throw new RuntimeException('slotOpportunities must be set for an AdSlotReport, it is required to calculate the relative fill rate for an WaterfallTagReport');
        }

        //Calculate ad tags first
        parent::doCalculateFields();

        $buyPrice = $this->getAdSlotBuyPrice($this->getAdSlot());
        $supplyCost = $this->calculateSupplyCost($this->getSlotOpportunities(), $this->getRefreshedSlotOpportunities(), $buyPrice);
        $this->setSupplyCost($supplyCost);

        $estProfit = $this->calculateEstProfit($this->getEstRevenue(), $this->getSupplyCost());
        $this->setEstProfit($estProfit);

        // difference calculate at ad slot level
        $this->setOpportunityFillRate($this->calculateOpportunityFillRate($this->getAdOpportunities(), $this->getSlotOpportunities()));
    }

    protected function aggregateSubReport(ReportInterface $subReport)
    {
        if (!$subReport instanceof AdTagReportInterface) {
            throw new InvalidArgumentException('Expected WaterfallTagReportInterface');
        }

        $subReport->setRelativeFillRate($this->getSlotOpportunities());

        if ($this->adSlot instanceof ReportableAdSlotInterface) {
            parent::aggregateSubReport($subReport);
        } else {
            $this->addTotalOpportunities($subReport->getTotalOpportunities());
            $this->addImpressions($subReport->getImpressions());
        }
    }

    protected function setDefaultName()
    {
        if ($this->adSlot instanceof ReportableAdSlotInterface) {
            $this->setName($this->adSlot->getName());
        }
    }
}