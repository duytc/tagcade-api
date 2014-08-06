<?php

namespace Tagcade\Model\Report\PerformanceReport\Display;

use Doctrine\Common\Collections\ArrayCollection;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Report\Behaviors\CalculatedDisplayReport;
use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Report\Behaviors\HasSubReports;

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
    use HasSubReports;

    public function __construct()
    {
        $this->subReports = new ArrayCollection();
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

        if ($this->getName() === null) {
            $this->setDefaultName();
        }
    }

    /**
     * @return void
     */
    abstract protected function setDefaultName();
}