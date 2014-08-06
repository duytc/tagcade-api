<?php

namespace Tagcade\Model\Report\PerformanceReport\Display;

use Doctrine\Common\Collections\ArrayCollection;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Report\Behaviors\CalculatedDisplayReport;
use Tagcade\Exception\RuntimeException;

/**
 * A calculated report contains sub reports
 *
 * i.e an ad slot report contains many ad tag reports
 *
 * These sub reports are used to generated the values for this report
 */
abstract class AbstractCalculatedReport implements CalculatedReportInterface
{
    use CalculatedDisplayReport;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $subReports;

    public function __construct()
    {
        $this->subReports = new ArrayCollection();
    }

    /**
     * @inheritdoc
     */
    public function addSubReport(ReportInterface $report)
    {
        if (!$this->isValidSubReport($report)) {
            throw new InvalidArgumentException('That sub report is valid for this report');
        }

        $report->setDate($this->getDate());
        $this->subReports->add($report);

        return $this;
    }

    public function setCalculatedFields()
    {
        $slotOpportunities = $totalOpportunities = $impressions = $passbacks = 0;

        foreach($this->subReports as $subReport) {
            if (!$subReport instanceof CalculatedReportInterface) {
                throw new RuntimeException('Sub reports must implement CalculatedReportInterface');
            }

            /** @var CalculatedReportInterface $subReport */
            $subReport->setCalculatedFields(); // chain the calls to setCalculatedFields

            $totalOpportunities += $subReport->getTotalOpportunities();
            $slotOpportunities += $subReport->getSlotOpportunities();
            $impressions += $subReport->getImpressions();
            $passbacks += $subReport->getPassbacks();

            unset($subReport);
        }

        $this->setTotalOpportunities($totalOpportunities);
        $this->setSlotOpportunities($slotOpportunities);
        $this->setImpressions($impressions);
        $this->setPassbacks($passbacks);

        $this->setFillRate();
    }
}