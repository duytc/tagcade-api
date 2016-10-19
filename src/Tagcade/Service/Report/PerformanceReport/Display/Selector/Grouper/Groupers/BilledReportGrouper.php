<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Selector\Grouper\Groupers;

use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Report\PerformanceReport\CalculateWeightedValueTrait;
use Tagcade\Model\Report\PerformanceReport\Display\BilledReportDataInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportDataInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Result\Group\BilledReportGroup;

class BilledReportGrouper extends AbstractGrouper
{
    use CalculateWeightedValueTrait;

    private $slotOpportunities;
    private $billedAmount;
    private $rtbImpressions;

    private $averageSlotOpportunities;
    private $averageRtbImpressions;
    private $averageBilledAmount;

    private $inBannerRequests;
    private $inBannerImpressions;
    private $inBannerTimeouts;
    private $inBannerBilledRate;
    private $inBannerBilledAmount;

    private $averageInBannerRequests;
    private $averageInBannerImpressions;
    private $averageInBannerTimeouts;
    private $averageInBannerBilledRate;
    private $averageInBannerBilledAmount;

    public function getGroupedReport()
    {
        return new BilledReportGroup(
            $this->getReportType(),
            $this->getStartDate(),
            $this->getEndDate(),
            $this->getReports(),
            $this->getReportName(),
            $this->getTotalOpportunities(),
            $this->getSlotOpportunities(), // added field
            $this->getImpressions(),
            $this->getPassbacks(),
            $this->getFillRate(),
            $this->getBilledAmount(),
            $this->getEstCpm(),
            $this->getEstRevenue(),

            $this->getAverageTotalOpportunities(),
            $this->getAverageImpressions(),
            $this->getAveragePassbacks(),
            $this->getAverageEstCpm(),
            $this->getAverageEstRevenue(),
            $this->getAverageFillRate(),
            $this->getAverageSlotOpportunities(),
            $this->getAverageBilledAmount(),
            $this->getRtbImpressions(),
            $this->getAverageRtbImpressions(),

            $this->getInBannerRequests(),
            $this->getInBannerTimeouts(),
            $this->getInBannerBilledAmount(),
            $this->getInBannerImpressions(),

            $this->getAverageInBannerRequests(),
            $this->getAverageInBannerTimeouts(),
            $this->getAverageInBannerBilledAmount(),
            $this->getAverageInBannerImpressions()
        );
    }

    protected  function groupReports(array $reports)
    {
        parent::groupReports($reports);

        $reportCount = count($this->getReports());

        $this->averageSlotOpportunities = $this->getRatio($this->getSlotOpportunities(), $reportCount);
        $this->averageBilledAmount = $this->getRatio($this->getBilledAmount(), $reportCount);
        $this->averageRtbImpressions = $this->getRatio($this->getRtbImpressions(), $reportCount);

        $this->averageInBannerTimeouts = $this->getRatio($this->getInBannerTimeouts(), $reportCount);
        $this->averageInBannerRequests = $this->getRatio($this->getInBannerRequests(), $reportCount);
        $this->averageInBannerImpressions = $this->getRatio($this->getInBannerImpressions(), $reportCount);
        $this->averageInBannerBilledAmount = $this->getRatio($this->getInBannerBilledAmount(), $reportCount);
        $this->averageInBannerBilledRate = $this->calculateWeightedValue($reports, 'inBannerBilledRate', 'inBannerBilledAmount');
    }

    protected function doGroupReport(ReportDataInterface $report)
    {
        if (!$report instanceof BilledReportDataInterface) {
            throw new InvalidArgumentException('Can only grouped BilledReportData instances');
        }

        parent::doGroupReport($report);

        $this->addSlotOpportunities($report->getSlotOpportunities());
        $this->addBilledAmount($report->getBilledAmount());
        $this->addRtbImpressions($report->getRtbImpressions());

        $this->addInBannerTimeouts($report->getInBannerTimeouts());
        $this->addInBannerRequests($report->getInBannerRequests());
        $this->addInBannerImpressions($report->getInBannerImpressions());
        $this->addInBannerBilledRate($report->getInBannerBilledRate());
        $this->addInBannerBilledAmount($report->getInBannerBilledAmount());
    }

    protected function addSlotOpportunities($slotOpportunities)
    {
        $this->slotOpportunities += (int) $slotOpportunities;
    }

    protected function addRtbImpressions($rtbImpressions)
    {
        $this->rtbImpressions += (int)$rtbImpressions;
    }

    protected function addBilledAmount($billedAmount)
    {
        $this->billedAmount += (float) $billedAmount;
    }

    protected function addInBannerRequests($inBannerRequests)
    {
        $this->inBannerRequests += (int) $inBannerRequests;
    }

    protected function addInBannerBilledAmount($inBannerBilledAmount)
    {
        $this->inBannerBilledAmount += (float) $inBannerBilledAmount;
    }

    protected function addInBannerBilledRate($inBannerBilledRate)
    {
        $this->inBannerBilledRate += (float) $inBannerBilledRate;
    }

    protected function addInBannerImpressions($inBannerImpressions)
    {
        $this->inBannerImpressions += (int) $inBannerImpressions;
    }

    protected function addInBannerTimeouts($inBannerTimeouts)
    {
        $this->inBannerTimeouts += (int) $inBannerTimeouts;
    }

    protected function calculateFillRate()
    {
        return $this->getPercentage($this->getImpressions(), $this->getSlotOpportunities());
    }

    /**
     * @return float
     */
    public function getSlotOpportunities()
    {
        return $this->slotOpportunities;
    }

    public function getRtbImpressions()
    {
        return $this->rtbImpressions;
    }

    /**
     * @return float
     */
    public function getBilledAmount()
    {
        return $this->billedAmount;
    }

    /**
     * @return int
     */
    public function getAverageSlotOpportunities()
    {
        return $this->averageSlotOpportunities;
    }

    public function getAverageRtbImpressions()
    {
        return $this->averageRtbImpressions;
    }

    /**
     * @return float
     */
    public function getAverageBilledAmount()
    {
        return $this->averageBilledAmount;
    }

    /**
     * @return mixed
     */
    public function getInBannerRequests()
    {
        return $this->inBannerRequests;
    }

    /**
     * @return mixed
     */
    public function getInBannerImpressions()
    {
        return $this->inBannerImpressions;
    }

    /**
     * @return mixed
     */
    public function getInBannerTimeouts()
    {
        return $this->inBannerTimeouts;
    }

    /**
     * @return mixed
     */
    public function getInBannerBilledRate()
    {
        return $this->inBannerBilledRate;
    }

    /**
     * @return mixed
     */
    public function getInBannerBilledAmount()
    {
        return $this->inBannerBilledAmount;
    }

    /**
     * @return mixed
     */
    public function getAverageInBannerRequests()
    {
        return $this->averageInBannerRequests;
    }

    /**
     * @return mixed
     */
    public function getAverageInBannerImpressions()
    {
        return $this->averageInBannerImpressions;
    }

    /**
     * @return mixed
     */
    public function getAverageInBannerTimeouts()
    {
        return $this->averageInBannerTimeouts;
    }

    /**
     * @return mixed
     */
    public function getAverageInBannerBilledRate()
    {
        return $this->averageInBannerBilledRate;
    }

    /**
     * @return mixed
     */
    public function getAverageInBannerBilledAmount()
    {
        return $this->averageInBannerBilledAmount;
    }
}