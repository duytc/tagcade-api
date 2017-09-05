<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Selector\Result\Group;


use Tagcade\Model\Report\PerformanceReport\Display\ImpressionBreakdownReportDataInterface;

class ImpressionBreakdownGroup extends ReportGroup implements ImpressionBreakdownReportDataInterface
{
    private $firstOpportunities;
    private $verifiedImpressions;
    private $unverifiedImpressions;
    private $blankImpressions;
    private $voidImpressions;
    private $clicks;
    private $refreshes;
    private $networkOpportunityFillRate;

    private $averageFirstOpportunities;
    private $averageVerifiedImpressions;
    private $averageUnverifiedImpressions;
    private $averageBlankImpressions;
    private $averageVoidImpressions;
    private $averageClicks;
    private $averageRefreshes;
    private $averageNetworkOpportunityFillRate;

    public function __construct($reportType, $startDate, $endDate, $reports, $name,
                                $totalOpportunities, $impressions, $passbacks, $fillRate, $estCpm, $estRevenue,
                                $averageTotalOpportunities, $averageImpressions, $averagePassbacks, $averageEstCpm, $averageEstRevenue, $averageFillRate,
                                $firstOpportunities, $verifiedImpressions, $unverifiedImpressions, $blankImpressions, $voidImpressions, $clicks, $refreshes, $adOpportunities, $networkOpportunityFillRate,
                                $averageFirstOpportunities, $averageVerifiedImpressions, $averageUnverifiedImpressions, $averageBlankImpressions, $averageVoidImpressions, $averageClicks, $averageRefreshes, $averageOpportunities, $averageNetworkOpportunityFillRate
    )
    {
        parent::__construct($reportType, $startDate, $endDate, $reports, $name,
            $totalOpportunities, $impressions, $passbacks, $fillRate, $estCpm, $estRevenue, $adOpportunities,
            $averageTotalOpportunities, $averageImpressions, $averagePassbacks, $averageEstCpm, $averageEstRevenue, $averageFillRate, $averageOpportunities
        );

        $this->firstOpportunities = $firstOpportunities;
        $this->verifiedImpressions = $verifiedImpressions;
        $this->unverifiedImpressions = $unverifiedImpressions;
        $this->blankImpressions = $blankImpressions;
        $this->voidImpressions = $voidImpressions;
        $this->clicks = $clicks;
        $this->refreshes = $refreshes;
        $this->networkOpportunityFillRate = $networkOpportunityFillRate;

        $this->averageFirstOpportunities = $averageFirstOpportunities;
        $this->averageVerifiedImpressions = $averageVerifiedImpressions;
        $this->averageUnverifiedImpressions = $averageUnverifiedImpressions;
        $this->averageBlankImpressions = $averageBlankImpressions;
        $this->averageVoidImpressions = $averageVoidImpressions;
        $this->averageClicks = $averageClicks;
        $this->averageRefreshes = $averageRefreshes;
        $this->averageNetworkOpportunityFillRate = $averageNetworkOpportunityFillRate;
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
    public function getClicks()
    {
        return $this->clicks;
    }

    /**
     * @return mixed
     */
    public function getRefreshes()
    {
        return $this->refreshes;
    }

    /**
     * @return float
     */
    public function getNetworkOpportunityFillRate()
    {
        return $this->networkOpportunityFillRate;
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
    public function getAverageVoidImpressions()
    {
        return $this->averageVoidImpressions;
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
    public function getAverageRefreshes()
    {
        return $this->averageRefreshes;
    }

    /**
     * @return mixed
     */
    public function getAverageNetworkOpportunityFillRate()
    {
        return $this->averageNetworkOpportunityFillRate;
    }
}