<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Selector\Result\Group;


use Tagcade\Model\Report\PerformanceReport\Display\ImpressionBreakdownReportDataInterface;

class ImpressionBreakdownGroup extends ReportGroup implements ImpressionBreakdownReportDataInterface
{

    private $firstOpportunities;
    private $verifiedImpressions;
    private $unverifiedImpressions;
    private $blankImpressions;

    private $averageFirstOpportunities;
    private $averageVerifiedImpressions;
    private $averageUnverifiedImpressions;
    private $averageBlankImpressions;

    public function __construct($reportType, $startDate, $endDate, $reports, $name,
        $totalOpportunities, $impressions, $passbacks, $fillRate, $estCpm, $estRevenue,
        $averageTotalOpportunities, $averageImpressions, $averagePassbacks, $averageEstCpm, $averageEstRevenue, $averageFillRate,
        $firstOpportunities, $verifiedImpressions, $unverifiedImpressions, $blankImpressions,
        $averageFirstOpportunities, $averageVerifiedImpressions, $averageUnverifiedImpressions, $averageBlankImpressions
    )
    {
        parent::__construct($reportType, $startDate, $endDate, $reports, $name,
            $totalOpportunities, $impressions, $passbacks, $fillRate, $estCpm, $estRevenue,
            $averageTotalOpportunities, $averageImpressions, $averagePassbacks, $averageEstCpm, $averageEstRevenue, $averageFillRate
        );

        $this->firstOpportunities = $firstOpportunities;
        $this->verifiedImpressions = $verifiedImpressions;
        $this->unverifiedImpressions = $unverifiedImpressions;
        $this->blankImpressions = $blankImpressions;

        $this->averageFirstOpportunities = $averageFirstOpportunities;
        $this->averageVerifiedImpressions = $averageVerifiedImpressions;
        $this->averageUnverifiedImpressions = $averageUnverifiedImpressions;
        $this->averageBlankImpressions = $averageBlankImpressions;
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

}