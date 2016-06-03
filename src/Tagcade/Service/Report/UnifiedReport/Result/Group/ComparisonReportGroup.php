<?php

namespace Tagcade\Service\Report\UnifiedReport\Result\Group;

use DateTime;
use Tagcade\Model\Report\CalculateComparisonRatiosTrait;
use Tagcade\Model\Report\CalculateRatiosTrait;
use Tagcade\Model\Report\PerformanceReport\Display\ReportDataInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Model\Report\UnifiedReport\Comparison\ComparisonReportInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Result\Group\ReportGroup;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Result\ReportResultInterface;

class ComparisonReportGroup extends ReportGroup implements ReportDataInterface, ReportResultInterface , ComparisonReportInterface
{
    use CalculateComparisonRatiosTrait;
    use CalculateRatiosTrait;
    protected $tagcadeFillRate;
    protected $tagcadePassbacks;
    protected $tagcadeTotalOpportunities;
    protected $tagcadeImpressions;
    protected $tagcadeEstCPM;
    protected $tagcadeEstRevenue;

    protected $partnerFillRate;
    protected $partnerPassbacks;
    protected $partnerTotalOpportunities;
    protected $partnerImpressions;
    protected $partnerEstCPM;
    protected $partnerEstRevenue;


    /**
     * ComparisonReportGroup constructor.
     * @param ReportTypeInterface|\Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface[] $reportType
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param array $reports
     * @param string $name
     * @param int $totalOpportunities
     * @param int $impressions
     * @param int $passbacks
     * @param float $fillRate
     * @param float $estCpm
     * @param $estRevenue
     * @param $averageTotalOpportunities
     * @param $averageImpressions
     * @param $averagePassbacks
     * @param $averageEstCpm
     * @param $averageEstRevenue
     * @param $averageFillRate
     * @param $tagcadeFillRate
     * @param $tagcadePassbacks
     * @param $tagcadeImpressions
     * @param $tagcadeTotalOpportunities
     * @param $tagcadeEstCpm
     * @param $tagcadeEstRevenue
     * @param $partnerFillRate
     * @param $partnerPassbacks
     * @param $partnerImpressions
     * @param $partnerTotalOpportunities
     * @param $partnerEstCpm
     * @param $partnerEstRevenue
     */
    public function __construct($reportType, DateTime $startDate, DateTime $endDate, array $reports, $name,
            $totalOpportunities, $impressions, $passbacks, $fillRate, $estCpm, $estRevenue,
            $averageTotalOpportunities, $averageImpressions, $averagePassbacks, $averageEstCpm, $averageEstRevenue, $averageFillRate,
            $tagcadeFillRate, $tagcadePassbacks, $tagcadeImpressions, $tagcadeTotalOpportunities, $tagcadeEstCpm, $tagcadeEstRevenue,
            $partnerFillRate, $partnerPassbacks, $partnerImpressions, $partnerTotalOpportunities, $partnerEstCpm, $partnerEstRevenue
    )
    {
        parent::__construct($reportType, $startDate, $endDate, $reports, $name,
        $totalOpportunities, $impressions, $passbacks, $fillRate, $estCpm, $estRevenue,
        $averageTotalOpportunities, $averageImpressions, $averagePassbacks, $averageEstCpm, $averageEstRevenue, $averageFillRate);

        $this->tagcadeEstRevenue = $tagcadeEstRevenue;
        $this->tagcadePassbacks = $tagcadePassbacks;
        $this->tagcadeEstCPM = $tagcadeEstCpm;
        $this->tagcadeFillRate = $tagcadeFillRate;
        $this->tagcadeImpressions = $tagcadeImpressions;
        $this->tagcadeTotalOpportunities = $tagcadeTotalOpportunities;

        $this->partnerTotalOpportunities = $partnerTotalOpportunities;
        $this->partnerEstRevenue = $partnerEstRevenue;
        $this->partnerEstCPM = $partnerEstCpm;
        $this->partnerFillRate = $partnerFillRate;
        $this->partnerImpressions = $partnerImpressions;
        $this->partnerPassbacks = $partnerPassbacks;

        $this->averageTotalOpportunities = round($averageTotalOpportunities, 4);
        $this->averageImpressions = round($averageImpressions, 4);
        $this->averagePassbacks = round($averagePassbacks, 4);
        $this->averageEstCpm = round($averageEstCpm, 4);
        $this->averageEstRevenue = round($averageEstRevenue, 4);
        $this->averageFillRate = round($averageFillRate, 4);
    }

    public function getPartnerFillRate()
    {
        return $this->getRatio($this->getPartnerImpressions(), $this->getPartnerTotalOpportunities());
    }

    public function getTagcadeFillRate()
    {
        return $this->getRatio($this->getTagcadeImpressions(), $this->getTagcadeTotalOpportunities());
    }

    public function getPartnerTotalOpportunities()
    {
        return $this->partnerTotalOpportunities;
    }

    public function getTagcadeTotalOpportunities()
    {
        return $this->tagcadeTotalOpportunities;
    }

    public function getPartnerImpressions()
    {
        return $this->partnerImpressions;
    }

    public function getTagcadeImpressions()
    {
        return $this->tagcadeImpressions;
    }

    public function getPartnerPassbacks()
    {
        return $this->partnerPassbacks;
    }

    public function getTagcadePassbacks()
    {
        return $this->tagcadePassbacks;
    }

    public function getPartnerEstCPM()
    {
        return $this->partnerEstCPM;
    }

    public function getTagcadeEstCPM()
    {
        return $this->tagcadeEstCPM;
    }

    public function getPartnerEstRevenue()
    {
        return $this->partnerEstRevenue;
    }

    public function getTagcadeEstRevenue()
    {
        return $this->tagcadeEstRevenue;
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
        return $this->getComparisonPercentage($this->getPartnerEstCPM(), $this->getTagcadeECPM());
    }

    public function getRevenueOpportunity()
    {
        $revenueOpportunities = round(($this->getTagcadeTotalOpportunities() - $this->getTagcadePassbacks() - $this->getPartnerImpressions()) * $this->getPartnerEstCPM() / 1000, 4);
        return  $revenueOpportunities < 0 ? 0 : $revenueOpportunities;
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