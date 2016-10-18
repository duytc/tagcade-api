<?php

namespace Tagcade\Model\Report\HeaderBiddingReport\Hierarchy\Platform;

use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\ReportableAdSlotInterface;
use Tagcade\Model\Report\HeaderBiddingReport\AbstractReport;
use Tagcade\Model\Report\HeaderBiddingReport\Fields\SuperReportTrait;
use Tagcade\Model\Report\HeaderBiddingReport\ReportInterface;

/**
 * The Ad Slot report extends the common AbstractCalculatedReport but it modifies the doCalculateFields method
 * because its sub reports are the core ad tag reports which do not have separate total and slot opportunities
 * So the doCalculateFields method is custom for this type of report
 */
class AdSlotReport extends AbstractReport implements AdSlotReportInterface
{
    use SuperReportTrait;

    /**
     * @var BaseAdSlotInterface
     */
    protected $adSlot;

    /**
     * @return BaseAdSlotInterface|null
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
        if ($this->adSlot instanceof ReportableAdSlotInterface) {
            return $this->adSlot->getId();
        }

        return null;
    }

    /**
     * @param BaseAdSlotInterface $adSlot
     * @return $this
     */
    public function setAdSlot(BaseAdSlotInterface $adSlot)
    {
        $this->adSlot = $adSlot;
        return $this;
    }

    public function isValidSuperReport(ReportInterface $report)
    {
        return $report instanceof SiteReportInterface;
    }

    protected function setDefaultName()
    {
        $this->setName($this->adSlot->getName());
    }
}