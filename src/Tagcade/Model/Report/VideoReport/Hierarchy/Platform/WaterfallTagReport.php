<?php


namespace Tagcade\Model\Report\VideoReport\Hierarchy\Platform;

use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Core\VideoWaterfallTagInterface;
use Tagcade\Model\Report\VideoReport\AbstractCalculatedReport as BaseAbstractCalculatedReport;
use Tagcade\Model\Report\VideoReport\Fields\SuperReportTrait;
use Tagcade\Model\Report\VideoReport\Hierarchy\Platform\Fields\VideoWaterfallTagTrait;
use Tagcade\Model\Report\VideoReport\ReportInterface;

class WaterfallTagReport extends BaseAbstractCalculatedReport implements WaterfallTagReportInterface
{
    use VideoWaterfallTagTrait;
    use SuperReportTrait;

    /** @var VideoWaterfallTagInterface */
    protected $videoWaterfallTag;

    /**
     * @var float
     */
    protected $customRate;

    public function isValidSubReport(ReportInterface $report)
    {
        return $report instanceof DemandAdTagReportInterface;
    }

    public function isValidSuperReport(ReportInterface $report)
    {
        return $report instanceof PublisherReportInterface;
    }

    public function getVideoWaterfallTag()
    {
        return $this->videoWaterfallTag;
    }

    public function setVideoWaterfallTag(VideoWaterfallTagInterface $videoWaterfallTag)
    {
        $this->videoWaterfallTag = $videoWaterfallTag;
        return $this;
    }

    public function getVideoWaterfallTagId()
    {
        if ($this->videoWaterfallTag instanceof VideoWaterfallTagInterface) {
            return $this->videoWaterfallTag->getId();
        }

        return null;
    }

    /**
     * @return float
     */
    public function getCustomRate()
    {
        return $this->customRate;
    }

    /**
     * @param float $customRate
     * @return self
     */
    public function setCustomRate($customRate)
    {
        $this->customRate = $customRate;
        return $this;
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

        return $this->getPercentage($this->getErrors(), $this->getBids());
    }

    public function setThresholdBilledAmount($chainToSubReports = true)
    {
        // TODO: Implement setThresholdBilledAmount() method.
    }

    protected function calculateEstSupplyCost()
    {
        $buyPrice = $this->getVideoWaterfallTag()->getBuyPrice();

        if (!is_numeric($buyPrice)) {
            return 0;
        }

        return $this->getRatio($buyPrice * $this->getImpressions(), 1000);
    }
}