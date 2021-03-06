<?php


namespace Tagcade\Model\Report\VideoReport\Hierarchy\DemandPartner;


use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Core\VideoDemandPartnerInterface;
use Tagcade\Model\Report\VideoReport\AbstractCalculatedReport;
use Tagcade\Model\Report\VideoReport\ReportInterface;

class DemandPartnerReport extends AbstractCalculatedReport implements DemandPartnerReportInterface
{
    /** @var VideoDemandPartnerInterface */
    protected $videoDemandPartner;

    protected function calculateFillRate()
    {
        if ($this->getRequests() === null) {
            throw new RuntimeException('request must be defined to calculate fill rates');
        }

        return $this->getPercentage($this->getImpressions(), $this->getRequests());
    }

    protected function calculateBidRate()
    {
        if ($this->getRequests() === null) {
            throw new RuntimeException('requests must be defined to calculate bid rates');
        }

        return $this->getPercentage($this->getBids(), $this->getRequests());
    }

    protected function calculateErrorRate()
    {
        if ($this->getBids() === null) {
            throw new RuntimeException('bids must be defined to calculate error rates');
        }

        return $this->getPercentage($this->getErrors(), $this->getBids());
    }

    public function getVideoDemandPartner()
    {
        return $this->videoDemandPartner;
    }

    public function setVideoDemandPartner(VideoDemandPartnerInterface $demandPartner)
    {
        $this->videoDemandPartner = $demandPartner;
        return $this;
    }

    public function getVideoDemandPartnerId()
    {
        if ($this->videoDemandPartner instanceof VideoDemandPartnerInterface) {
            return $this->videoDemandPartner->getId();
        }

        return null;
    }

    public function isValidSubReport(ReportInterface $report)
    {
        return $report instanceof DemandAdTagReportInterface;
    }

    public function getDate()
    {
        return $this->date;
    }

    protected function calculateEstSupplyCost()
    {
        // TODO: Implement calculateEstSupplyCost() method.
    }
}