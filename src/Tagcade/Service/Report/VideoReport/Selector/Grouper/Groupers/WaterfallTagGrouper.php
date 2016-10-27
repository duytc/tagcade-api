<?php


namespace Tagcade\Service\Report\VideoReport\Selector\Grouper\Groupers;


use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Report\PerformanceReport\CalculateWeightedValueTrait;
use Tagcade\Model\Report\VideoReport\AdTagReportDataInterface;
use Tagcade\Model\Report\VideoReport\ReportDataInterface;
use Tagcade\Model\Report\VideoReport\ReportType\Hierarchy\DemandPartner\DemandAdTag as PartnerDemandAdTagReportType;
use Tagcade\Model\Report\VideoReport\ReportType\Hierarchy\DemandPartner\DemandPartner as PartnerDemandPartnerReportType;
use Tagcade\Model\Report\VideoReport\ReportType\Hierarchy\Platform\DemandAdTag as PlatformDemandAdTagReportType;
use Tagcade\Service\Report\VideoReport\Selector\Result\CalculatedReportGroupInterface;
use Tagcade\Service\Report\VideoReport\Selector\Result\Group\WaterfallTagReportGroup;

class WaterfallTagGrouper extends AbstractGrouper
{
    use CalculateWeightedValueTrait;

    private $adTagRequests;
    private $adTagErrors;
    private $adTagBids;
    private $billedAmount;
    private $estSupplyCost;
    private $netRevenue;

    private $averageAdTagRequest;
    private $averageAdTagError;
    private $averageAdTagBid;
    private $averageBilledAmount;
    private $averageEstSupplyCost;
    private $averageNetRevenue;
    private $billedRate;

    protected function doGroupReport(ReportDataInterface $report)
    {
        parent::doGroupReport($report);

        if ($report instanceof AdTagReportDataInterface || $report instanceof CalculatedReportGroupInterface) {
            $this->addAdTagBids($report->getAdTagBids());
            $this->addAdTagErrors($report->getAdTagErrors());
            $this->addAdTagRequests($report->getAdTagRequests());
            $this->addAdBilledAmount($report->getBilledAmount());
            $this->addEstSupplyCost($report->getEstSupplyCost());
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
            $this->getRequestFillRate(),
            $this->getClicks(),
            $this->getClickThroughRate(),
            $this->getAverageRequests(),
            $this->getAverageBids(),
            $this->getAverageBidRate(),
            $this->getAverageErrors(),
            $this->getAverageErrorRate(),
            $this->getAverageImpressions(),
            $this->getAverageRequestFillRate(),
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
            $this->getBilledRate(),
            $this->getEstDemandRevenue(),
            $this->getAverageEstDemandRevenue(),
            $this->getEstSupplyCost(),
            $this->getAverageEstSupplyCost(),
            $this->getNetRevenue(),
            $this->getAverageNetRevenue()
        );
    }

    protected function groupReports(array $reports)
    {
        parent::groupReports($reports);

        if (!$this->getReportType() instanceof PartnerDemandAdTagReportType &&
            !$this->getReportType() instanceof PlatformDemandAdTagReportType &&
            !$this->getReportType() instanceof PartnerDemandPartnerReportType
        ) {
            $this->billedRate = $this->calculateWeightedValue($reports, 'billedRate', 'billedAmount');
        }

        $reportCount = count($this->getReports());

        $this->setNetRevenue();

        $this->averageEstSupplyCost = $this->getRatio($this->getEstSupplyCost(), $reportCount);
        $this->averageAdTagBid = $this->getRatio($this->getAdTagBids(), $reportCount);
        $this->averageAdTagError = $this->getRatio($this->getAdTagErrors(), $reportCount);
        $this->averageAdTagRequest = $this->getRatio($this->getAdTagRequests(), $reportCount);
        $this->averageBilledAmount = $this->getRatio($this->getBilledAmount(), $reportCount);
        $this->averageNetRevenue = $this->getRatio($this->getNetRevenue(), $reportCount);
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
            $this->billedAmount += (float) $billedAmount;
        }
    }

    protected function addEstSupplyCost($estSupplyCost)
    {
        if (!is_null($estSupplyCost)) {
            $this->estSupplyCost += (int) $estSupplyCost;
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

    /**
     * @return mixed
     */
    public function getEstSupplyCost()
    {
        return $this->estSupplyCost;
    }

    /**
     * @return mixed
     */
    public function getNetRevenue()
    {
        return $this->netRevenue;
    }

    /**
     * @return mixed
     */
    public function getAverageEstSupplyCost()
    {
        return $this->averageEstSupplyCost;
    }

    /**
     * @return mixed
     */
    public function getAverageNetRevenue()
    {
        return $this->averageNetRevenue;
    }

    public function setNetRevenue()
    {
        $this->netRevenue = $this->getEstDemandRevenue() - $this->getEstSupplyCost();
    }
}