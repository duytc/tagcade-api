<?php


namespace Tagcade\Service\Report\SourceReport\Result\Group;


use Tagcade\Service\Report\SourceReport\Result\ReportResultInterface;

class ReportGroup implements ReportResultInterface
{
    protected $reports;
    protected $name;
    protected $date;
    protected $startDate;
    protected $endDate;

    protected $displayOpportunities;
    protected $displayImpressions;
    protected $displayFillRate;
    protected $displayClicks;
    protected $displayCTR;
    protected $displayIPV;
    protected $videoPlayerReady;
    protected $videoAdPlays;
    protected $videoAdImpressions;
    protected $videoAdCompletions;
    protected $videoAdCompletionRate;
    protected $videoIPV;
    protected $videoAdClicks;
    protected $videoStarts;
    protected $videoEnds;
    protected $visits;
    protected $pageViews;
    protected $qtos;
    protected $qtosPercentage;
    protected $billedRate;
    protected $billedAmount;

    protected $averageVisits;
    protected $averagePageViews;
    protected $averageBilledAmount;

    /**
     * ReportGroup constructor.
     * @param $reports
     * @param $name
     * @param $date
     * @param $startDate
     * @param $endDate
     * @param $displayOpportunities
     * @param $displayImpressions
     * @param $displayFillRate
     * @param $displayClicks
     * @param $displayCTR
     * @param $displayIPV
     * @param $videoPlayerReady
     * @param $videoAdPlays
     * @param $videoAdImpressions
     * @param $videoAdCompletions
     * @param $videoAdCompletionRate
     * @param $videoIPV
     * @param $videoAdClicks
     * @param $videoStarts
     * @param $videoEnds
     * @param $visits
     * @param $pageViews
     * @param $qtos
     * @param $qtosPercentage
     * @param $billedRate
     * @param $billedAmount
     * @param $averageVisits
     * @param $averagePageViews
     * @param $averageBilledAmount
     */
    public function __construct($reports, $name, $date, $startDate, $endDate, $displayOpportunities, $displayImpressions, $displayFillRate,
        $displayClicks, $displayCTR, $displayIPV, $videoPlayerReady, $videoAdPlays, $videoAdImpressions, $videoAdCompletions, $videoAdCompletionRate,
        $videoIPV, $videoAdClicks, $videoStarts, $videoEnds, $visits, $pageViews, $qtos, $qtosPercentage, $billedRate, $billedAmount, $averageVisits, $averagePageViews, $averageBilledAmount)
    {
        $this->reports = $reports;
        $this->name = $name;
        $this->date = $date;
        $this->startDate = $startDate;
        $this->endDate = $endDate;

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
        $this->videoAdCompletionRate = round($videoAdCompletionRate, 4);
        $this->videoIPV = $videoIPV;
        $this->videoAdClicks = $videoAdClicks;
        $this->videoStarts = $videoStarts;
        $this->videoEnds = $videoEnds;
        $this->visits = $visits;
        $this->pageViews = $pageViews;
        $this->qtos = $qtos;
        $this->qtosPercentage = $qtosPercentage;
        $this->billedRate = round($billedRate, 4);
        $this->billedAmount = round($billedAmount, 4);
        $this->averageVisits = round($averageVisits, 4);
        $this->averagePageViews = round($averagePageViews, 4);
        $this->averageBilledAmount = round($averageBilledAmount, 4);
    }

    /**
     * @return mixed
     */
    public function getReports()
    {
        return $this->reports;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return mixed
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @return mixed
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @return mixed
     */
    public function getDisplayOpportunities()
    {
        return $this->displayOpportunities;
    }

    /**
     * @return mixed
     */
    public function getDisplayImpressions()
    {
        return $this->displayImpressions;
    }

    /**
     * @return mixed
     */
    public function getDisplayFillRate()
    {
        return $this->displayFillRate;
    }

    /**
     * @return mixed
     */
    public function getDisplayClicks()
    {
        return $this->displayClicks;
    }

    /**
     * @return mixed
     */
    public function getDisplayCTR()
    {
        return $this->displayCTR;
    }

    /**
     * @return mixed
     */
    public function getDisplayIPV()
    {
        return $this->displayIPV;
    }

    /**
     * @return mixed
     */
    public function getVideoPlayerReady()
    {
        return $this->videoPlayerReady;
    }

    /**
     * @return mixed
     */
    public function getVideoAdPlays()
    {
        return $this->videoAdPlays;
    }

    /**
     * @return mixed
     */
    public function getVideoAdImpressions()
    {
        return $this->videoAdImpressions;
    }

    /**
     * @return mixed
     */
    public function getVideoAdCompletions()
    {
        return $this->videoAdCompletions;
    }

    /**
     * @return float
     */
    public function getVideoAdCompletionRate()
    {
        return $this->videoAdCompletionRate;
    }

    /**
     * @return mixed
     */
    public function getVideoIPV()
    {
        return $this->videoIPV;
    }

    /**
     * @return mixed
     */
    public function getVideoAdClicks()
    {
        return $this->videoAdClicks;
    }

    /**
     * @return mixed
     */
    public function getVideoStarts()
    {
        return $this->videoStarts;
    }

    /**
     * @return mixed
     */
    public function getVideoEnds()
    {
        return $this->videoEnds;
    }

    /**
     * @return mixed
     */
    public function getVisits()
    {
        return $this->visits;
    }

    /**
     * @return mixed
     */
    public function getPageViews()
    {
        return $this->pageViews;
    }

    /**
     * @return mixed
     */
    public function getQtos()
    {
        return $this->qtos;
    }

    /**
     * @return mixed
     */
    public function getQtosPercentage()
    {
        return $this->qtosPercentage;
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

    /**
     * @return float
     */
    public function getAverageVisits()
    {
        return $this->averageVisits;
    }

    /**
     * @return float
     */
    public function getAveragePageViews()
    {
        return $this->averagePageViews;
    }

    /**
     * @return float
     */
    public function getAverageBilledAmount()
    {
        return $this->averageBilledAmount;
    }
}