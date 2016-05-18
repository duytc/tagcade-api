<?php


namespace Tagcade\Model\Report\UnifiedReport\Comparison;
use Tagcade\Model\Report\CalculateComparisonRatiosTrait;
use Tagcade\Model\Report\PerformanceReport\Display\AbstractReport as BaseAbstractReport;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;

abstract class AbstractReport extends BaseAbstractReport implements ComparisonReportInterface, ReportInterface
{
    use CalculateComparisonRatiosTrait;
    /**
     * @return mixed
     */
    public function getRevenueOpportunity()
    {
        $revenueOpportunities = round(($this->getTagcadeTotalOpportunities() - $this->getTagcadePassbacks() - $this->getPartnerImpressions()) * $this->getPartnerEstCPM() / 1000, 4);
        return  $revenueOpportunities < 0 ? 0 : $revenueOpportunities;
    }

    public function setTotalOpportunities($totalOpportunities)
    {
        $this->totalOpportunities = $totalOpportunities;

        return $this;
    }

    public function setPassbacks($passbacks)
    {
        $this->passbacks = $passbacks;

        return $this;
    }

    public function getTagcadeECPM()
    {
        return $this->getRatio(
            $this->getPartnerEstRevenue(),
            $this->getTagcadeTotalOpportunities() - $this->getPartnerPassbacks()
        ) * 1000;
    }

    public function getECPMComparison()
    {
        return $this->getComparisonPercentage($this->getTagcadeECPM(), $this->getPartnerEstCPM());
    }

    public function getTotalOpportunityComparison()
    {
        return $this->getComparisonPercentage($this->getTagcadeTotalOpportunities(), $this->getPartnerTotalOpportunities());
    }

    public function getPassbacksComparison()
    {
        return $this->getComparisonPercentage($this->getTagcadePassbacks(), $this->getPartnerPassbacks());
    }
}