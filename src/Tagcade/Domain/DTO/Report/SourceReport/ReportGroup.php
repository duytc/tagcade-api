<?php

namespace Tagcade\Domain\DTO\Report\SourceReport;

class ReportGroup
{
    /**
     * @var int
     */
    protected $displayOpportunities;

    /**
     * @var int
     */
    protected $displayImpressions;

    /**
     * @var float
     */
    protected $displayFillRate;

    /**
     * @var int
     */
    protected $displayClicks;

    /**
     * @var float
     */
    protected $displayCTR;

    /**
     * @var float
     */
    protected $displayIPV;

    /**
     * @var int
     */
    protected $videoPlayerReady;

    /**
     * @var int
     */
    protected $videoAdPlays;

    /**
     * @var int
     */
    protected $videoAdImpressions;

    /**
     * @var int
     */
    protected $videoAdCompletions;

    /**
     * @var float
     */
    protected $videoAdCompletionRate;

    /**
     * @var float
     */
    protected $videoIPV;

    /**
     * @var int
     */
    protected $videoAdClicks;

    /**
     * @var int
     */
    protected $videoStarts;

    /**
     * @var int
     */
    protected $videoEnds;

    /**
     * @var int
     */
    protected $visits;

    /**
     * @var int
     */
    protected $pageViews;

    /**
     * @var int
     */
    protected $qtos;

    /**
     * @var float
     */
    protected $billedRate;

    /**
     * @var float
     */
    protected $billedAmount;

    /**
     * @var float
     */
    protected $qtosPercentage;

    /**
     * @var int
     */
    protected $averageVisits;

    /**
     * @var int
     */
    protected $averagePageViews;

    protected $viewsPerVisit;

    public function __construct(
        $displayOpportunities, $displayImpressions, $displayFillRate, $displayClicks, $displayCTR, $displayIPV, $videoPlayerReady,
        $videoAdPlays, $videoAdImpressions, $videoAdCompletions, $videoAdCompletionRate, $videoIPV, $videoAdClicks, $videoStarts,
        $videoEnds, $visits, $pageViews, $qtos, $billedRate, $billedAmount, $qtosPercentage, $averageVisits, $averagePageViews, $viewsPerVisit) {

        $this->displayOpportunities = $displayOpportunities;
        $this->displayImpressions = $displayImpressions;
        $this->displayFillRate = $displayFillRate;
        $this->displayClicks = $displayClicks;
        $this->displayCTR = $displayCTR;
        $this->displayIPV = $displayIPV;
        $this->videoPlayerReady = $videoPlayerReady;
        $this->videoAdPlays = $videoAdPlays;
        $this->videoAdImpressions = $videoAdImpressions;
        $this->videoAdCompletions = $videoAdCompletions;
        $this->videoAdCompletionRate = $videoAdCompletionRate;
        $this->videoIPV = $videoIPV;
        $this->videoAdClicks = $videoAdClicks;
        $this->videoStarts = $videoStarts;
        $this->videoEnds = $videoEnds;
        $this->visits = $visits;
        $this->pageViews = $pageViews;
        $this->qtos = $qtos;
        $this->billedRate = $billedRate;
        $this->billedAmount = $billedAmount;
        $this->qtosPercentage = $qtosPercentage;
        $this->averageVisits = $averageVisits;
        $this->averagePageViews = $averagePageViews;
        $this->viewsPerVisit = $viewsPerVisit;
    }

    /**
     * @return float
     */
    public function getDisplayCTR()
    {
        return $this->displayCTR;
    }

    /**
     * @return int
     */
    public function getDisplayClicks()
    {
        return $this->displayClicks;
    }

    /**
     * @return float
     */
    public function getDisplayFillRate()
    {
        return $this->displayFillRate;
    }

    /**
     * @return float
     */
    public function getDisplayIPV()
    {
        return $this->displayIPV;
    }

    /**
     * @return int
     */
    public function getDisplayImpressions()
    {
        return $this->displayImpressions;
    }

    /**
     * @return int
     */
    public function getDisplayOpportunities()
    {
        return $this->displayOpportunities;
    }

    /**
     * @return int
     */
    public function getPageViews()
    {
        return $this->pageViews;
    }

    /**
     * @return int
     */
    public function getQtos()
    {
        return $this->qtos;
    }

    /**
     * @return float
     */
    public function getQtosPercentage()
    {
        return $this->qtosPercentage;
    }

    /**
     * @return int
     */
    public function getVideoAdClicks()
    {
        return $this->videoAdClicks;
    }

    /**
     * @return float
     */
    public function getVideoAdCompletionRate()
    {
        return $this->videoAdCompletionRate;
    }

    /**
     * @return int
     */
    public function getVideoAdCompletions()
    {
        return $this->videoAdCompletions;
    }

    /**
     * @return int
     */
    public function getVideoAdImpressions()
    {
        return $this->videoAdImpressions;
    }

    /**
     * @return int
     */
    public function getVideoAdPlays()
    {
        return $this->videoAdPlays;
    }

    /**
     * @return int
     */
    public function getVideoEnds()
    {
        return $this->videoEnds;
    }

    /**
     * @return float
     */
    public function getVideoIPV()
    {
        return $this->videoIPV;
    }

    /**
     * @return int
     */
    public function getVideoPlayerReady()
    {
        return $this->videoPlayerReady;
    }

    /**
     * @return int
     */
    public function getVideoStarts()
    {
        return $this->videoStarts;
    }

    /**
     * @return int
     */
    public function getVisits()
    {
        return $this->visits;
    }

    /**
     * @return int
     */
    public function getAverageVisits()
    {
        return $this->averageVisits;
    }

    /**
     * @return int
     */
    public function getAveragePageViews()
    {
        return $this->averagePageViews;
    }

    /**
     * @return float
     */
    public function getViewsPerVisit()
    {
        return $this->viewsPerVisit;
    }

    /**
     * @return float
     */
    public function getBilledRate()
    {
        return $this->billedRate;
    }

    /**
     * @return float
     */
    public function getBilledAmount()
    {
        return $this->billedAmount;
    }
}