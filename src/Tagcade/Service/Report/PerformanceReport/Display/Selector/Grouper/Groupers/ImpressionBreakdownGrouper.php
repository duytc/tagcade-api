<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Selector\Grouper\Groupers;


use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Report\PerformanceReport\Display\ImpressionBreakdownReportDataInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportDataInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Result\Group\ImpressionBreakdownGroup;

class ImpressionBreakdownGrouper extends AbstractGrouper
{
    private $firstOpportunities;
    private $verifiedImpressions;
    private $unverifiedImpressions;
    private $blankImpressions;
    private $voidImpressions;
    private $clicks;

    private $averageFirstOpportunities;
    private $averageVerifiedImpressions;
    private $averageUnverifiedImpressions;
    private $averageBlankImpressions;
    private $averageVoidImpressions;
    private $averageClicks;

    public function getGroupedReport()
    {
        return new ImpressionBreakdownGroup(
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

            $this->getFirstOpportunities(),
            $this->getVerifiedImpressions(),
            $this->getUnverifiedImpressions(),
            $this->getBlankImpressions(),
            $this->getVoidImpressions(),
            $this->getClicks(),

            $this->getAverageFirstOpportunities(),
            $this->getAverageVerifiedImpressions(),
            $this->getAverageUnverifiedImpressions(),
            $this->getAverageBlankImpressions(),
            $this->getAverageVoidImpressions(),
            $this->getAverageClicks()
        );
    }


    protected function groupReports(array $reports)
    {
        parent::groupReports($reports);

        $reportCount = count($this->getReports());

        $this->averageFirstOpportunities = $this->getRatio($this->getFirstOpportunities(), $reportCount);
        $this->averageVerifiedImpressions = $this->getRatio($this->getVerifiedImpressions(), $reportCount);
        $this->averageUnverifiedImpressions = $this->getRatio($this->getUnverifiedImpressions(), $reportCount);
        $this->averageBlankImpressions = $this->getRatio($this->getBlankImpressions(), $reportCount);
        $this->averageVoidImpressions = $this->getRatio($this->getVoidImpressions(), $reportCount);
        $this->averageClicks = $this->getRatio($this->getClicks(), $reportCount);
    }


    protected function doGroupReport(ReportDataInterface $report)
    {
        if (!$report instanceof ImpressionBreakdownReportDataInterface) {
            throw new InvalidArgumentException('Can only grouped ImpressionBreakdownReport instances');
        }

        parent::doGroupReport($report);

        $this->addFirstOpportunities($report->getFirstOpportunities());
        $this->addVerifiedImpressions($report->getVerifiedImpressions());
        $this->addUnverifiedImpressions($report->getUnverifiedImpressions());
        $this->addBlankImpressions($report->getBlankImpressions());
        $this->addVoidImpressions($report->getBlankImpressions());
        $this->addClicks($report->getClicks());

    }

    protected function addFirstOpportunities($firstOpportunities)
    {
        $this->firstOpportunities += (int)$firstOpportunities;
    }

    protected function addVerifiedImpressions($verifiedImpressions)
    {
        $this->verifiedImpressions += (int)$verifiedImpressions;
    }

    protected function addUnverifiedImpressions($unverifiedImpressions)
    {
        $this->unverifiedImpressions += (int)$unverifiedImpressions;
    }

    protected function addBlankImpressions($blankImpressions)
    {
        $this->blankImpressions += (int)$blankImpressions;
    }

    protected function addVoidImpressions($voidImpressions)
    {
        $this->voidImpressions += (int)$voidImpressions;
    }

    protected function addClicks($clicks)
    {
        $this->clicks += (int)$clicks;
    }

    /**
     * @return mixed
     */
    public function getBlankImpressions()
    {
        return $this->blankImpressions;
    }

    /**
     * @return mixed
     */
    public function getFirstOpportunities()
    {
        return $this->firstOpportunities;
    }

    /**
     * @return mixed
     */
    public function getUnverifiedImpressions()
    {
        return $this->unverifiedImpressions;
    }

    /**
     * @return mixed
     */
    public function getVerifiedImpressions()
    {
        return $this->verifiedImpressions;
    }

    /**
     * @return mixed
     */
    public function getVoidImpressions()
    {
        return $this->voidImpressions;
    }

    /**
     * @return mixed
     */
    public function getClicks()
    {
        return $this->clicks;
    }


    /**
     * @return mixed
     */
    public function getAverageFirstOpportunities()
    {
        return $this->averageFirstOpportunities;
    }

    /**
     * @return mixed
     */
    public function getAverageVerifiedImpressions()
    {
        return $this->averageVerifiedImpressions;
    }

    /**
     * @return mixed
     */
    public function getAverageUnverifiedImpressions()
    {
        return $this->averageUnverifiedImpressions;
    }

    /**
     * @return mixed
     */
    public function getAverageBlankImpressions()
    {
        return $this->averageBlankImpressions;
    }

    /**
     * @return mixed
     */
    public function getAverageClicks()
    {
        return $this->averageClicks;
    }

    /**
     * @return mixed
     */
    public function getAverageVoidImpressions()
    {
        return $this->averageVoidImpressions;
    }



} 