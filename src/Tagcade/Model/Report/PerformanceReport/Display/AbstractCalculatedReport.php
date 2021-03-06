<?php

namespace Tagcade\Model\Report\PerformanceReport\Display;

use Doctrine\Common\Collections\ArrayCollection;
use Tagcade\Model\Report\PerformanceReport\CalculateWeightedValueTrait;
use Tagcade\Model\Report\PerformanceReport\Display\Fields\SubReportsTrait;

abstract class AbstractCalculatedReport extends AbstractReport
{
    use SubReportsTrait;
    use CalculateWeightedValueTrait;

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
        $this->totalOpportunities = 0;
        $this->impressions = 0;
        $this->passbacks = 0;
        $this->estRevenue = 0;
        $this->adOpportunities = 0;
        $this->inBannerTimeouts = 0;
        $this->inBannerRequests = 0;
        $this->inBannerImpressions = 0;
    }

    protected function doCalculateFields()
    {
        $this->resetCounts();

        foreach ($this->subReports as $subReport) {
            /** @var ReportInterface $subReport */
            $subReport->setCalculatedFields(); // chain the calls to setCalculatedFields

            $this->aggregateSubReport($subReport);

            unset($subReport);
        }

        $this->setEstCpm($this->calculateWeightedValue($this->getSubReports(), $frequency = 'estCpm', $weight = 'estRevenue'));
    }

    protected function aggregateSubReport(ReportInterface $subReport)
    {
        $this->addTotalOpportunities($subReport->getTotalOpportunities());
        $this->addImpressions($subReport->getImpressions());
        $this->addPassbacks($subReport->getPassbacks());
        $this->addEstRevenue($subReport->getEstRevenue());
        $this->addAdOpportunities($subReport->getAdOpportunities());
        $this->addInBannerTimeouts($subReport->getInBannerTimeouts());
        $this->addInBannerRequests($subReport->getInBannerRequests());
        $this->addInBannerImpressions($subReport->getInBannerImpressions());
    }

    protected function addTotalOpportunities($totalOpportunities)
    {
        $this->totalOpportunities += (int)$totalOpportunities;
    }

    protected function addImpressions($impressions)
    {
        $this->impressions += (int)$impressions;
    }

    protected function addPassbacks($passbacks)
    {
        $this->passbacks += (int)$passbacks;
    }

    protected function addEstRevenue($estRevenue)
    {
        $this->estRevenue += (float)$estRevenue;
    }

    protected function addAdOpportunities($adOpportunities)
    {
        $this->adOpportunities += (int)$adOpportunities;
    }

    protected function addInBannerRequests($inBannerRequests)
    {
        $this->inBannerRequests += (int)$inBannerRequests;
    }

    protected function addInBannerImpressions($inBannerImpressions)
    {
        $this->inBannerImpressions += (int)$inBannerImpressions;
    }

    protected function addInBannerTimeouts($inBannerTimeouts)
    {
        $this->inBannerTimeouts += (int)$inBannerTimeouts;
    }
}