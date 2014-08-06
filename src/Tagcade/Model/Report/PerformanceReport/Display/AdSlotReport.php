<?php

namespace Tagcade\Model\Report\PerformanceReport\Display;

use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Report\Behaviors\CalculatedDisplayReport;

/**
 * The Ad Slot report extends the common AbstractCalculatedReport but it modifies the setCalculatedFields method
 * because its sub reports are the core ad tag reports which do not have separate total and slot opportunities
 * So the setCalculatedFields method is custom for this type of report
 */
class AdSlotReport extends AbstractCalculatedReport implements AdSlotReportInterface
{
    use CalculatedDisplayReport;

    protected $adSlot;

    public function isValidSubReport(ReportInterface $report)
    {
        return $report instanceof AdTagReportInterface;
    }

    /**
     * Overwrite the parent setCalculatedFields
     *
     * @throws \Tagcade\Exception\RuntimeException
     */
    public function setCalculatedFields()
    {
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
    }
}