<?php
namespace Tagcade\Service\Report\PerformanceReport\Display\Selector\Grouper\Groupers;

use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Report\PerformanceReport\CalculateAdOpportunitiesTrait;
use Tagcade\Model\Report\PerformanceReport\CalculateWeightedValueTrait;
use Tagcade\Model\Report\PerformanceReport\Display\BilledReportDataInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportDataInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Result\Group\BilledReportGroup;

class BilledReportGrouper extends AbstractGrouper
{
    use CalculateWeightedValueTrait;
    use CalculateAdOpportunitiesTrait;

    private $slotOpportunities;
    private $opportunityFillRate; // only for platform slot/site/account/platform level
    private $billedAmount;
    private $supplyCost;
    private $estProfit;

    private $averageSupplyCost;
    private $averageEstProfit;

    private $averageSlotOpportunities;
    private $averageOpportunityFillRate; // only for platform slot/site/account/platform level
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

    private $totalOpportunityFillRate; // temp for calculate this $averageOpportunityFillRate

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
            $this->getAdOpportunities(),
            $this->getSupplyCost(),
            $this->getEstProfit(),
            $this->getAverageSupplyCost(),
            $this->getAverageEstProfit(),
            $this->getOpportunityFillRate(),

            $this->getAverageTotalOpportunities(),
            $this->getAverageImpressions(),
            $this->getAveragePassbacks(),
            $this->getAverageEstCpm(),
            $this->getAverageEstRevenue(),
            $this->getAverageFillRate(),
            $this->getAverageSlotOpportunities(),
            $this->getAverageBilledAmount(),

            $this->getInBannerRequests(),
            $this->getInBannerTimeouts(),
            $this->getInBannerBilledAmount(),
            $this->getInBannerImpressions(),

            $this->getAverageInBannerRequests(),
            $this->getAverageInBannerTimeouts(),
            $this->getAverageInBannerBilledAmount(),
            $this->getAverageInBannerImpressions(),
            $this->getAverageAdOpportunities(),
            $this->getAverageOpportunityFillRate()
        );
    }

    protected function groupReports(array $reports)
    {
        parent::groupReports($reports);

        $this->opportunityFillRate = $this->calculateOpportunityFillRate($this->getAdOpportunities(), $this->getSlotOpportunities());

        $reportCount = count($this->getReports());

        $this->averageSlotOpportunities = $this->getRatio($this->getSlotOpportunities(), $reportCount);
        $this->averageOpportunityFillRate = $this->getRatio($this->totalOpportunityFillRate, $reportCount);
        $this->averageBilledAmount = $this->getRatio($this->getBilledAmount(), $reportCount);
        $this->averageSupplyCost = $this->getRatio($this->getSupplyCost(), $reportCount);
        $this->averageEstProfit = $this->getRatio($this->getEstProfit(), $reportCount);

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
        $this->addOpportunityFillRate($report->getOpportunityFillRate());
        $this->addBilledAmount($report->getBilledAmount());
        $this->addSupplyCost($report->getSupplyCost());
        $this->addEstProfit($report->getEstProfit());

        $this->addInBannerBilledRate($report->getInBannerBilledRate());
        $this->addInBannerBilledAmount($report->getInBannerBilledAmount());
    }

    protected function addSlotOpportunities($slotOpportunities)
    {
        $this->slotOpportunities += (int)$slotOpportunities;
    }

    protected function addOpportunityFillRate($opportunityFillRate)
    {
        $this->totalOpportunityFillRate += (float)$opportunityFillRate;
    }

    protected function addBilledAmount($billedAmount)
    {
        $this->billedAmount += (float)$billedAmount;
    }

    /**
     * @param $supplyCost
     * @return $this
     */
    private function addSupplyCost($supplyCost)
    {
        $this->supplyCost += (float) $supplyCost;

        return $this;
    }

    /**
     * @param $estProfit
     * @return $this
     */
    private function addEstProfit($estProfit)
    {
        $this->estProfit += (float) $estProfit;

        return $this;
    }

    protected function addInBannerRequests($inBannerRequests)
    {
        $this->inBannerRequests += (int)$inBannerRequests;
    }

    protected function addInBannerBilledAmount($inBannerBilledAmount)
    {
        $this->inBannerBilledAmount += (float)$inBannerBilledAmount;
    }

    protected function addInBannerBilledRate($inBannerBilledRate)
    {
        $this->inBannerBilledRate += (float)$inBannerBilledRate;
    }

    protected function addInBannerImpressions($inBannerImpressions)
    {
        $this->inBannerImpressions += (int)$inBannerImpressions;
    }

    protected function addInBannerTimeouts($inBannerTimeouts)
    {
        $this->inBannerTimeouts += (int)$inBannerTimeouts;
    }

    protected function calculateFillRate()
    {
        return $this->getPercentage($this->getImpressions(), $this->getSlotOpportunities());
    }

    /**
     * @return int
     */
    public function getSlotOpportunities()
    {
        return $this->slotOpportunities;
    }

    /**
     * @return float
     */
    public function getOpportunityFillRate()
    {
        return $this->opportunityFillRate;
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

    /**
     * @return float
     */
    public function getAverageOpportunityFillRate()
    {
        return $this->averageOpportunityFillRate;
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
     * @inheritdoc
     */
    public function getSupplyCost()
    {
        return $this->supplyCost;
    }

    /**
     * @inheritdoc
     */
    public function getEstProfit()
    {
        return $this->estProfit;
    }

    /**
     * @inheritdoc
     */
    public function getAverageSupplyCost()
    {
        return $this->averageSupplyCost;
    }

    /**
     * @inheritdoc
     */
    public function getAverageEstProfit()
    {
        return $this->averageEstProfit;
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