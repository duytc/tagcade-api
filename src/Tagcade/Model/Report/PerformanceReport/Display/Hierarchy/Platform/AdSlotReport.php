<?php

namespace Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform;

use Tagcade\Model\Report\PerformanceReport\Display\Fields\SuperReportTrait;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Model\Core\AdSlotInterface;
use Tagcade\Exception\RuntimeException;

/**
 * The Ad Slot report extends the common AbstractCalculatedReport but it modifies the doCalculateFields method
 * because its sub reports are the core ad tag reports which do not have separate total and slot opportunities
 * So the doCalculateFields method is custom for this type of report
 */
class AdSlotReport extends AbstractCalculatedReport implements AdSlotReportInterface
{
    const REPORT_TYPE = 'platform.adSlot';

    use SuperReportTrait;

    /**
     * @var AdSlotInterface
     */
    protected $adSlot;

    /**
     * @return AdSlotInterface|null
     */
    public function getAdSlot()
    {
        return $this->adSlot;
    }

    /**
     * @return int|null
     */
    public function getAdSlotId()
    {
        if ($this->adSlot instanceof AdSlotInterface) {
            return $this->adSlot->getId();
        }

        return null;
    }

    /**
     * @param AdSlotInterface $adSlot
     * @return $this
     */
    public function setAdSlot(AdSlotInterface $adSlot)
    {
        $this->adSlot = $adSlot;
        return $this;
    }

    public function isValidSubReport(ReportInterface $report)
    {
        return $report instanceof AdTagReportInterface;
    }

    public function isValidSuperReport(ReportInterface $report)
    {
        return $report instanceof SiteReportInterface;
    }

    protected function doCalculateFields()
    {
        if ($this->slotOpportunities === null) {
            throw new RuntimeException('slotOpportunities must be set for an AdSlotReport, it is required to calculate the relative fill rate for an AdTagReport');
        }

        $totalOpportunities = $impressions = $passbacks = $estRevenue = 0;

        foreach($this->subReports as $adTagReport) {
            if (!$this->isValidSubReport($adTagReport)) {
                throw new RuntimeException('That sub report is not valid for this report');
            }

            $adTagReport->setRelativeFillRate($this->getSlotOpportunities());

            /** @var AdTagReportInterface $adTagReport */
            $adTagReport->setCalculatedFields(); // chain the calls to setCalculatedFields

            $totalOpportunities += $adTagReport->getTotalOpportunities();
            $impressions += $adTagReport->getImpressions();
            $passbacks += $adTagReport->getPassbacks();
            $estRevenue += $adTagReport->getEstRevenue();

            unset($adTagReport);
        }

        $this->setTotalOpportunities($totalOpportunities);
        $this->setImpressions($impressions);
        $this->setPassbacks($passbacks);
        $this->setEstRevenue($estRevenue);
        $this->setEstCpm($this->calculateEstCpm());
    }

    protected function setDefaultName()
    {
        if ($this->adSlot instanceof AdSlotInterface) {
            $this->setName($this->adSlot->getName());
        }
    }
}