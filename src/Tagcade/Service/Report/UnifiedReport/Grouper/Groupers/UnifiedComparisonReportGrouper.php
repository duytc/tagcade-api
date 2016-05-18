<?php

namespace Tagcade\Service\Report\UnifiedReport\Grouper\Groupers;

use Tagcade\Model\Report\CalculateComparisonRatiosTrait;
use Tagcade\Model\Report\CalculateRatiosTrait;
use Tagcade\Model\Report\UnifiedReport\Comparison\ComparisonReportInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Grouper\Groupers\AbstractGrouper;
use Tagcade\Service\Report\UnifiedReport\Result\Group\ComparisonReportGroup;


class UnifiedComparisonReportGrouper extends AbstractGrouper
{
    use CalculateComparisonRatiosTrait;
    use CalculateRatiosTrait;

    protected $totalPartnerImpressions = 0;
    protected $totalTagcadeImpressions = 0;
    protected $totalPartnerFillRate = 0;
    protected $totalTagcadeFillRate = 0;
    protected $totalPartnerPassbacks = 0;
    protected $totalTagcadePassbacks = 0;
    protected $totalPartnerEstCpm = 0;
    protected $totalTagcadeEstCpm = 0;
    protected $totalPartnerEstRevenue = 0;
    protected $totalTagcadeEstRevenue = 0;
    protected $totalPartnerTotalOpportunities = 0;
    protected $totalTagcadeTotalOpportunities = 0;

    private $totalImpressions;
    private $totalPassbacks;
    private $totalOpportunities;
    private $totalFillRate;
    private $totalEstCpm;
    private $totalEstRevenue;

    protected function groupReports(array $reports)
    {
        /** @var ComparisonReportInterface $report */
        foreach ($this->getReports() as $report) {
            $this->addUnifiedEstRevenue($report->getPartnerEstRevenue());
            $this->addTagcadeEstRevenue($report->getTagcadeEstRevenue());
            $this->addUnifiedImpressions($report->getPartnerImpressions());
            $this->addTagcadeImpressions($report->getTagcadeImpressions());
            $this->addUnifiedPassbacks($report->getPartnerPassbacks());
            $this->addTagcadePassbacks($report->getTagcadePassbacks());
            $this->addUnifiedOpportunities($report->getPartnerTotalOpportunities());
            $this->addTagcadeOpportunities($report->getTagcadeTotalOpportunities());

            $this->addFillRateRatio($this->getComparisonPercentage($report->getTagcadeFillRate(), $report->getPartnerFillRate()));
            $this->addImpressionsRatio($this->getComparisonPercentage($report->getTagcadeImpressions(), $report->getPartnerImpressions()));
            $this->addOpportunitiesRatio($this->getComparisonPercentage($report->getTagcadeTotalOpportunities(), $report->getPartnerTotalOpportunities()));
            $this->addPassbacksRatio($this->getComparisonPercentage($report->getTagcadePassbacks(), $report->getPartnerPassbacks()));
            $this->addEstCpmRatio($this->getComparisonPercentage($report->getTagcadeEstCPM(), $report->getPartnerEstCPM()));
            $this->addEstRevenueRatio($this->getComparisonPercentage($report->getTagcadeEstRevenue(), $report->getPartnerEstRevenue()));
        }

        $this->calculateTotalPartnerCpm();

        $this
            ->setImpressions($this->getComparisonPercentage($this->totalTagcadeImpressions, $this->totalPartnerImpressions))
            ->setTotalOpportunities($this->getComparisonPercentage($this->totalTagcadeTotalOpportunities, $this->totalPartnerTotalOpportunities))
            ->setPassbacks($this->getComparisonPercentage($this->totalTagcadePassbacks, $this->totalPartnerPassbacks))
            ->setEstCpm($this->getComparisonPercentage($this->totalTagcadeEstCpm, $this->totalPartnerEstCpm))
            ->setEstRevenue($this->getComparisonPercentage($this->totalTagcadeEstRevenue, $this->totalPartnerEstRevenue))
            ->setFillRate();;

        $reportCount = count($this->getReports());
        $this->setAverageTotalOpportunities($this->getRatio($this->totalOpportunities, $reportCount))
            ->setAveragePassbacks($this->getRatio($this->totalPassbacks, $reportCount))
            ->setAverageImpressions($this->getRatio($this->totalImpressions, $reportCount))
            ->setAverageFillRate($this->getRatio($this->totalFillRate, $reportCount))
            ->setAverageEstCpm($this->getRatio($this->totalEstCpm, $reportCount))
            ->setAverageEstRevenue($this->getRatio($this->totalEstRevenue, $reportCount));
    }

    public function getGroupedReport()
    {
        return new ComparisonReportGroup(
            $this->getReportType(),
            $this->getStartDate(),
            $this->getEndDate(),
            $this->getReports(),
            $this->getReportName(),
            $this->getTotalOpportunities(),
            $this->getImpressions(),
            $this->getPassbacks(),
            $this->getFillRate(),
            $this->getEstCpm(),
            $this->getEstRevenue(),
            $this->getAverageTotalOpportunities(),
            $this->getAverageImpressions(),
            $this->getAveragePassbacks(),
            $this->getAverageEstCpm(),
            $this->getAverageEstRevenue(),
            $this->getAverageFillRate(),
            $this->getTotalTagcadeFillRate(),
            $this->getTotalTagcadePassbacks(),
            $this->getTotalTagcadeImpressions(),
            $this->getTotalTagcadeTotalOpportunities(),
            $this->getTotalTagcadeEstCpm(),
            $this->getTotalTagcadeEstRevenue(),
            $this->getTotalPartnerFillRate(),
            $this->getTotalPartnerPassbacks(),
            $this->getTotalPartnerImpressions(),
            $this->getTotalPartnerTotalOpportunities(),
            $this->getTotalPartnerEstCpm(),
            $this->getTotalPartnerEstRevenue()
        );
    }

    protected function calculateFillRate()
    {
        return $this->getRatio($this->totalPartnerFillRate, $this->totalTagcadeFillRate);
    }

    private function addImpressionsRatio($impressions)
    {
        $this->totalImpressions += $impressions;
    }

    private function addUnifiedImpressions($impression)
    {
        if ($impression === null) return $this;

        $this->totalPartnerImpressions += $impression;
        return $this;
    }

    private function addTagcadeImpressions($impression)
    {
        if ($impression === null) return $this;

        $this->totalTagcadeImpressions += $impression;
        return $this;
    }

    private function addPassbacksRatio($passbacks)
    {
        $this->totalPassbacks += $passbacks;
    }

    private function addUnifiedPassbacks($passbacks)
    {
        if ($passbacks === null) return $this;

        $this->totalPartnerPassbacks += $passbacks;
        return $this;
    }

    private function addTagcadePassbacks($passbacks)
    {
        if ($passbacks === null) return $this;

        $this->totalTagcadePassbacks += $passbacks;
        return $this;
    }

    private function addFillRateRatio($fillRate)
    {
        $this->totalFillRate += $fillRate;
    }

    private function addEstCpmRatio($estCpm)
    {
        $this->totalEstCpm += $estCpm;
    }

    private function calculateTotalPartnerCpm()
    {
        $this->totalPartnerEstCpm = 1000 * $this->getRatio($this->getTotalPartnerEstRevenue(), $this->getTotalPartnerImpressions());
    }

    private function addEstRevenueRatio($estRevenue)
    {
        $this->totalEstRevenue += $estRevenue;
    }

    private function addUnifiedEstRevenue($estRevenue)
    {
        if ($estRevenue === null) return $this;

        $this->totalPartnerEstRevenue += $estRevenue;
        return $this;
    }

    private function addTagcadeEstRevenue($estRevenue)
    {
        if ($estRevenue === null) return $this;

        $this->totalTagcadeEstRevenue += $estRevenue;
        return $this;
    }

    private function addOpportunitiesRatio($opportunities)
    {
        $this->totalOpportunities += $opportunities;
    }

    private function addUnifiedOpportunities($opportunities)
    {
        if ($opportunities === null) return $this;

        $this->totalPartnerTotalOpportunities += $opportunities;
        return $this;
    }

    private function addTagcadeOpportunities($opportunities)
    {
        if ($opportunities === null) return $this;

        $this->totalTagcadeTotalOpportunities += $opportunities;
        return $this;
    }

    /**
     * @return int
     */
    public function getTotalPartnerImpressions()
    {
        return $this->totalPartnerImpressions;
    }

    /**
     * @return int
     */
    public function getTotalTagcadeImpressions()
    {
        return $this->totalTagcadeImpressions;
    }

    /**
     * @return int
     */
    public function getTotalPartnerFillRate()
    {
        return $this->totalPartnerFillRate;
    }

    /**
     * @return int
     */
    public function getTotalTagcadeFillRate()
    {
        return $this->totalTagcadeFillRate;
    }

    /**
     * @return int
     */
    public function getTotalPartnerPassbacks()
    {
        return $this->totalPartnerPassbacks;
    }

    /**
     * @return int
     */
    public function getTotalTagcadePassbacks()
    {
        return $this->totalTagcadePassbacks;
    }

    /**
     * @return int
     */
    public function getTotalPartnerEstCpm()
    {
        return $this->totalPartnerEstCpm;
    }

    /**
     * @return int
     */
    public function getTotalTagcadeEstCpm()
    {
        return $this->totalTagcadeEstCpm;
    }

    /**
     * @return int
     */
    public function getTotalPartnerEstRevenue()
    {
        return $this->totalPartnerEstRevenue;
    }

    /**
     * @return int
     */
    public function getTotalTagcadeEstRevenue()
    {
        return $this->totalTagcadeEstRevenue;
    }

    /**
     * @return int
     */
    public function getTotalPartnerTotalOpportunities()
    {
        return $this->totalPartnerTotalOpportunities;
    }

    /**
     * @return int
     */
    public function getTotalTagcadeTotalOpportunities()
    {
        return $this->totalTagcadeTotalOpportunities;
    }
}