<?php


namespace Tagcade\Model\Report\RtbReport\Hierarchy;

use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\DisplayAdSlotInterface;
use Tagcade\Model\Core\NativeAdSlotInterface;
use Tagcade\Model\Core\ReportableAdSlotInterface;
use Tagcade\Model\Report\RtbReport\AbstractReport;
use Tagcade\Model\Report\RtbReport\Fields\SuperReportTrait;
use Tagcade\Model\Report\RtbReport\ReportInterface;

class AdSlotReport extends AbstractReport implements AdSlotReportInterface
{
    use SuperReportTrait;

    /**
     * @var BaseAdSlotInterface
     */
    protected $adSlot;

    /**
     * @return DisplayAdSlotInterface|NativeAdSlotInterface|null
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
        if ($this->adSlot instanceof ReportableAdSlotInterface) {
            $this->setName($this->adSlot->getName());
        }
    }

    /**
     * @inheritdoc
     */
    protected function calculateFillRate()
    {
        if ($this->getOpportunities() === null) {
            throw new RuntimeException('total opportunities must be defined to calculate ad tag fill rates');
        }

        // note that we use slot opportunities to calculate fill rate in this Reports
        return $this->getPercentage($this->getImpressions(), $this->getOpportunities());
    }
}