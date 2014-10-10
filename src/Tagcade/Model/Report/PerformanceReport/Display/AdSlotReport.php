<?php

namespace Tagcade\Model\Report\PerformanceReport\Display;

use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Core\AdSlotInterface;

/**
 * The Ad Slot report extends the common AbstractCalculatedReport but it modifies the setCalculatedFields method
 * because its sub reports are the core ad tag reports which do not have separate total and slot opportunities
 * So the setCalculatedFields method is custom for this type of report
 */
class AdSlotReport extends AbstractCalculatedReportWithSuper implements AdSlotReportInterface
{
    protected $adSlot;

    /**
     * @return AdSlotInterface|null
     */
    public function getAdSlot()
    {
        return $this->adSlot;
    }

    /**
     * @param AdSlotInterface $adSlot
     * @return self
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

    /**
     * Overwrite the parent setCalculatedFields
     *
     * @throws \Tagcade\Exception\RuntimeException
     */
    public function setCalculatedFields()
    {
        if ($this->slotOpportunities === null) {
            throw new RuntimeException('slotOpportunities must be set for AdSlotReports, it is required to calculate the relative fill rate for AdTagReports');
        }

        $totalOpportunities = $impressions = $passbacks = 0;

        foreach($this->subReports as $adTagReport) {
            if (!$this->isValidSubReport($adTagReport)) {
                throw new RuntimeException('Sub reports must implement AdTagReportInterface');
            }

            /** @var AdTagReportInterface $adTagReport */
            $adTagReport->setCalculatedFields(); // chain the calls to setCalculatedFields

            $adTagReport->setRelativeFillRate($this->getSlotOpportunities());

            $totalOpportunities += $adTagReport->getOpportunities();
            $impressions += $adTagReport->getImpressions();
            $passbacks += $adTagReport->getPassbacks();

            unset($adTagReport);
        }

        $this->setTotalOpportunities($totalOpportunities);
        $this->setImpressions($impressions);
        $this->setPassbacks($passbacks);

        $this->setFillRate();

        if ($this->getName() === null) {
            $this->setDefaultName();
        }
    }

    protected function setDefaultName()
    {
        if ($adSlot = $this->getAdSlot()) {
            $this->setName($adSlot->getName());
        }
    }
}