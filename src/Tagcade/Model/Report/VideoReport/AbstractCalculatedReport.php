<?php


namespace Tagcade\Model\Report\VideoReport;


use Doctrine\Common\Collections\ArrayCollection;
use Tagcade\Model\Report\VideoReport\Fields\SubReportsTrait;
use Tagcade\Model\Report\VideoReport\Hierarchy\Platform\AccountReportInterface;

abstract class AbstractCalculatedReport extends AbstractReport
{
    use SubReportsTrait;

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
        // Empty for now since some reports in hierarchy Platform and AdNetwork don't require
    }

    protected function resetCounts()
    {
        $this->requests = 0;
        $this->impressions = 0;
        $this->bids = 0;
        $this->errors = 0;
        $this->bids = 0;
        $this->blocks =0;
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
}