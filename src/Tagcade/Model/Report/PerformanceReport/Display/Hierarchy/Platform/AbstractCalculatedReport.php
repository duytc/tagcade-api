<?php

namespace Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform;

use Tagcade\Model\Report\PerformanceReport\Display\AbstractCalculatedReport as BaseAbstractCalculatedReport;
use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Report\PerformanceReport\Display\SuperReportInterface;

/**
 * A calculated report in the platform Reports contains sub reports
 *
 * i.e an ad slot report contains many ad tag reports
 *
 * These sub reports are used to generated the values for this report
 */
abstract class AbstractCalculatedReport extends BaseAbstractCalculatedReport implements CalculatedReportInterface, SuperReportInterface
{
    protected $slotOpportunities;

    /**
     * @return int|null
     */
    public function getSlotOpportunities()
    {
        return $this->slotOpportunities;
    }

    /**
     * @param int $slotOpportunities
     * @return $this
     */
    public function setSlotOpportunities($slotOpportunities)
    {
        $this->slotOpportunities = (int) $slotOpportunities;

        return $this;
    }

    /**
     * @inheritdoc
     */
    protected function setFillRate()
    {
        if ($this->getSlotOpportunities() === null) {
            throw new RuntimeException('slot opportunities must be defined to calculate fill rates');
        }

        // note that we use slot opportunities to calculate fill rate in this Reports except for AdTagReport
        $this->fillRate = $this->getPercentage($this->getImpressions(), $this->getSlotOpportunities());

        return $this;
    }

    protected function doCalculateFields()
    {
        parent::doCalculateFields();

        // Set slot opportunities for Platform, Account, and Site
        if( !$this instanceof AdSlotReportInterface) {
            $this->setSlotOpportunities($this->_doCalculateSlotOpportunities());
        }
    }

    private function _doCalculateSlotOpportunities()
    {
        $slotOpportunities = 0;
        foreach($this->subReports as $subReport) {
            if (!$this->isValidSubReport($subReport)) {
                throw new RuntimeException('That sub report is not valid for this report');
            }

            // Slot opportunities calculated for Platform, Account and Site only.
            if($subReport instanceof CalculatedReportInterface){
                $slotOpportunities += $subReport->getSlotOpportunities();
            }

            unset($subReport);
        }

        return $slotOpportunities;
    }
}