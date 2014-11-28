<?php

namespace Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform;

use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Report\PerformanceReport\Display\Fields\SuperReportTrait;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform\Fields\SlotOpportunitiesTrait;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Model\Core\AdSlotInterface;
use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Report\PerformanceReport\Display\AbstractCalculatedReport as BaseAbstractCalculatedReport;

/**
 * The Ad Slot report extends the common AbstractCalculatedReport but it modifies the doCalculateFields method
 * because its sub reports are the core ad tag reports which do not have separate total and slot opportunities
 * So the doCalculateFields method is custom for this type of report
 */
class AdSlotReport extends BaseAbstractCalculatedReport implements AdSlotReportInterface
{
    use SuperReportTrait;
    use SlotOpportunitiesTrait;

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
            throw new RuntimeException('slotOpportunities must be set for an AdSlotReport, it is required to calculate the relative fill rate for an AdTagReport');
        }

        parent::doCalculateFields();
    }

    protected function aggregateSubReport(ReportInterface $subReport)
    {
        if (!$subReport instanceof AdTagReportInterface) {
            throw new InvalidArgumentException('Expected AdTagReportInterface');
        }

        $subReport->setRelativeFillRate($this->getSlotOpportunities());

        parent::aggregateSubReport($subReport);

    }

    protected function setDefaultName()
    {
        if ($this->adSlot instanceof AdSlotInterface) {
            $this->setName($this->adSlot->getName());
        }
    }


}