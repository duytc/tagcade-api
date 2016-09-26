<?php


namespace Tagcade\Model\Report\VideoReport\Hierarchy\Platform;

use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Core\VideoDemandAdTagInterface;
use Tagcade\Model\Report\VideoReport\AbstractReport;
use Tagcade\Model\Report\VideoReport\Fields\SuperReportTrait;
use Tagcade\Model\Report\VideoReport\ReportInterface;

class DemandAdTagReport extends AbstractReport implements DemandAdTagReportInterface
{
    use SuperReportTrait;

    /** @var VideoDemandAdTagInterface */
    protected $videoDemandAdTag;


    public function isValidSuperReport(ReportInterface $report)
    {
        return $report instanceof WaterfallTagReportInterface;
    }

    public function getVideoDemandAdTag()
    {
        return $this->videoDemandAdTag;
    }

    public function setVideoDemandAdTag(VideoDemandAdTagInterface $videoDemandAdTag)
    {
        $this->videoDemandAdTag = $videoDemandAdTag;
    }

    public function getVideoDemandAdTagId()
    {
        if ($this->videoDemandAdTag instanceof VideoDemandAdTagInterface) {
            return $this->videoDemandAdTag->getId();
        }

        return null;
    }

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

        return $this->getRatio($this->getErrors(), $this->getBids());
    }
}