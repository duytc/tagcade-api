<?php


namespace Tagcade\Service\Report\VideoReport\Selector\Grouper\Groupers;


use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Report\PerformanceReport\CalculateWeightedValueTrait;
use Tagcade\Model\Report\VideoReport\AdTagReportDataInterface;
use Tagcade\Model\Report\VideoReport\ReportDataInterface;
use Tagcade\Service\Report\VideoReport\Selector\Result\CalculatedReportGroupInterface;
use Tagcade\Service\Report\VideoReport\Selector\Result\Group\WaterfallTagReportGroup;

class WaterfallTagGrouper extends AbstractGrouper
{
    use CalculateWeightedValueTrait;

    private $adTagRequests;
    private $adTagErrors;
    private $adTagBids;
    private $billedAmount;

    private $averageAdTagRequest;
    private $averageAdTagError;
    private $averageAdTagBid;
    private $averageBilledAmount;

    private $billedRate;

    protected function doGroupReport(ReportDataInterface $report)
    {
        parent::doGroupReport($report);

        if ($report instanceof AdTagReportDataInterface || $report instanceof CalculatedReportGroupInterface) {
            $this->addAdTagBids($report->getAdTagBids());
            $this->addAdTagErrors($report->getAdTagErrors());
            $this->addAdTagRequests($report->getAdTagRequests());
            $this->addAdBilledAmount($report->getBilledAmount());
        } else {
            $this->setAdTagBids(null) ;
            $this->setAdTagErrors(null);
            $this->setAdTagRequests(null);
            $this->setBilledAmount(null);
        }
    }

    public function getGroupedReport()
    {
        return new WaterfallTagReportGroup(
            $this->getReportType(),
            $this->getReports(),
            $this->getRequests(),
            $this->getBids(),
            $this->getBidRate(),
            $this->getErrors(),
            $this->getErrorRate(),
            $this->getImpressions(),
            $this->getFillRate(),
            $this->getClicks(),
            $this->getClickThroughRate(),
            $this->getAverageRequests(),
            $this->getAverageBids(),
            $this->getAverageBidRate(),
            $this->getAverageErrors(),
            $this->getAverageErrorRate(),
            $this->getAverageImpressions(),
            $this->getAverageFillRate(),
            $this->getAverageClicks(),
            $this->getAverageClickThroughRate(),
            $this->getStartDate(),
            $this->getEndDate(),
            $this->getBlocks(),
            $this->getAverageBlocks(),
            $this->getAdTagRequests(),
            $this->getAdTagBids(),
            $this->getAdTagErrors(),
            $this->getAverageAdTagRequest(),
            $this->getAverageAdTagBid(),
            $this->getAverageAdTagError(),
            $this->getBilledAmount(),
            $this->getAverageBilledAmount(),
            $this->getBilledRate()
        );
    }

    protected function groupReports(array $reports)
    {
        parent::groupReports($reports);

        $this->billedRate = $this->calculateWeightedValue($reports, 'billedRate', 'billedAmount');

        $reportCount = count($this->getReports());

        $this->averageAdTagBid = $this->getRatio($this->getAdTagBids(), $reportCount);
        $this->averageAdTagError = $this->getRatio($this->getAdTagErrors(), $reportCount);
        $this->averageAdTagRequest = $this->getRatio($this->getAdTagRequests(), $reportCount);
        $this->averageBilledAmount = $this->getRatio($this->getBilledAmount(), $reportCount);

    }

    protected function addAdTagBids($adTagBids)
    {
        if(!is_null($adTagBids)) {
            $this->adTagBids += (int) $adTagBids;
        }
    }

    protected function addAdTagErrors($adTagErrors)
    {
        if (!is_null($adTagErrors)) {
            $this->adTagErrors += (int) $adTagErrors;
        }
    }

    protected function addAdTagRequests($adTagRequests)
    {
        if (!is_null($adTagRequests)) {
            $this->adTagRequests += (int) $adTagRequests;
        }
    }

    protected function addAdBilledAmount($billedAmount)
    {
        if (!is_null($billedAmount)) {
            $this->billedAmount += (int) $billedAmount;
        }
    }
    /**
     * @return mixed
     */
    public function getAdTagRequests()
    {
        return $this->adTagRequests;
    }

    /**
     * @return mixed
     */
    public function getAdTagErrors()
    {
        return $this->adTagErrors;
    }

    /**
     * @return mixed
     */
    public function getAdTagBids()
    {
       return $this->adTagBids;
    }

    /**
     * @return mixed
     */
    public function getAverageAdTagRequest()
    {
        return $this->averageAdTagRequest;
    }

    /**
     * @return mixed
     */
    public function getAverageAdTagError()
    {
        return $this->averageAdTagError;
    }

    /**
     * @return mixed
     */
    public function getAverageAdTagBid()
    {
        return $this->averageAdTagBid;
    }

    /**
     * @param mixed $adTagBids
     */
    public function setAdTagBids($adTagBids)
    {
        $this->adTagBids = $adTagBids;
    }

    /**
     * @param mixed $adTagErrors
     */
    public function setAdTagErrors($adTagErrors)
    {
        $this->adTagErrors = $adTagErrors;
    }

    /**
     * @param mixed $adTagRequests
     */
    public function setAdTagRequests($adTagRequests)
    {
        $this->adTagRequests = $adTagRequests;
    }

    /**
     * @return mixed
     */
    public function getAverageBilledAmount()
    {
        return $this->averageBilledAmount;
    }

    /**
     * @return mixed
     */
    public function getBilledAmount()
    {
        return $this->billedAmount;
    }

    /**
     * @param mixed $billedAmount
     */
    public function setBilledAmount($billedAmount)
    {
        $this->billedAmount = $billedAmount;
    }

    /**
     * @return mixed
     */
    public function getBilledRate()
    {
        return $this->billedRate;
    }
}