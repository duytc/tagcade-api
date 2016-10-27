<?php


namespace Tagcade\Model\Report\VideoReport;


use Doctrine\Common\Collections\ArrayCollection;
use Tagcade\Model\Report\VideoReport\Fields\SubReportsTrait;
use Tagcade\Model\Report\VideoReport\Hierarchy\Platform\AccountReportInterface;

abstract class AbstractCalculatedReport extends AbstractReport
{
    use SubReportsTrait;

    /**
     * @var float
     */
    protected $estSupplyCost;

    /**
     * @var float
     */
    protected $netRevenue;

    public function __construct()
    {
        $this->subReports = new ArrayCollection();
    }

    public function setCalculatedFields()
    {
        $this->doCalculateFields();

        parent::setCalculatedFields();

        $this->postCalculateFields();
    }

    /**
     *  use if some fields need to be calculated base on calculated fields
     */
    protected function postCalculateFields()
    {
        $this->setEstSupplyCost();
        $this->setNetRevenue();
    }

    protected function resetCounts()
    {
        $this->requests = 0;
        $this->impressions = 0;
        $this->bids = 0;
        $this->errors = 0;
        $this->bids = 0;
        $this->blocks = 0;
        $this->estSupplyCost = 0;
        $this->estDemandRevenue = 0;
    }

    protected function doCalculateFields()
    {
        $this->resetCounts();

        foreach($this->subReports as $subReport) {
            /** @var ReportInterface $subReport */
            $subReport->setCalculatedFields(); // chain the calls to setCalculatedFields

            $this->aggregateSubReport($subReport);

            unset($subReport);
        }
    }

    protected function aggregateSubReport(ReportInterface $subReport)
    {
        $this->addRequests($subReport->getRequests());
        $this->addImpressions($subReport->getImpressions());
        $this->addBids($subReport->getBids());
        $this->addClicks($subReport->getClicks());
        $this->addErrors($subReport->getErrors());
        $this->addBlocks($subReport->getBlocks());
        $this->addEstDemandRevenue($subReport->getEstDemandRevenue());
    }

    protected function addRequests($requests)
    {
        $this->requests += (int)$requests;
    }

    protected function addImpressions($impressions)
    {
        $this->impressions += (int)$impressions;
    }

    protected function addErrors($errors)
    {
        $this->errors += (int)$errors;
    }

    protected function addBids($bids)
    {
        $this->bids += (int)$bids;
    }

    protected function addClicks($clicks)
    {
        $this->clicks += (int)$clicks;
    }

    protected function addBlocks($blocks)
    {
        $this->blocks += (int)$blocks;
    }

    protected function addEstDemandRevenue($estDemandRevenue)
    {
        $this->estDemandRevenue += (float) $estDemandRevenue;
    }

    protected function calculateEstDemandRevenue()
    {
        return $this->estDemandRevenue;
    }

    public function setEstSupplyCost()
    {
        $this->estSupplyCost = $this->calculateEstSupplyCost();
    }

    public function setNetRevenue()
    {
        $this->netRevenue = $this->estDemandRevenue - $this->estSupplyCost;
    }

    abstract protected function calculateEstSupplyCost();

    /**
     * @return float
     */
    public function getEstSupplyCost()
    {
        return $this->estSupplyCost;
    }

    /**
     * @return float
     */
    public function getNetRevenue()
    {
        return $this->netRevenue;
    }
}