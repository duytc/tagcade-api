<?php

namespace Tagcade\Model\Report\SourceReport;

class Record
{
    protected $id;

    /**
     * @var Report
     */
    protected $sourceReport;

    /**
     * @var array
     */
    protected $trackingKeys1;

    /**
     * @var array
     */
    protected $trackingKeys;

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
    protected $qtosPercentage;

    public function __construct()
    {
        $this->trackingKeys = [];
    }

    /**
     * @return Report
     */
    public function getSourceReport()
    {
        return $this->sourceReport;
    }

    /**
     * @param Report $sourceReport
     * @return $this
     */
    public function setSourceReport(Report $sourceReport)
    {
        $this->sourceReport = $sourceReport;

        return $this;
    }

    public function getTrackingKeys()
    {
        return $this->trackingKeys;
    }

    /**
     * @param TrackingKey $trackingKey
     * @return $this
     */
    public function addTrackingKey(TrackingKey $trackingKey)
    {
        $this->trackingKeys[] = $trackingKey;

        return $this;
    }

    /**
     * @return float
     */
    public function getDisplayCTR()
    {
        return $this->displayCTR;
    }

    /**
     * @param float $displayCTR
     * @return $this
     */
    public function setDisplayCTR($displayCTR)
    {
        $this->displayCTR = $displayCTR;

        return $this;
    }

    /**
     * @return int
     */
    public function getDisplayClicks()
    {
        return $this->displayClicks;
    }

    /**
     * @param int $displayClicks
     * @return $this
     */
    public function setDisplayClicks($displayClicks)
    {
        $this->displayClicks = $displayClicks;

        return $this;
    }

    /**
     * @return float
     */
    public function getDisplayFillRate()
    {
        return $this->displayFillRate;
    }

    /**
     * @param float $displayFillRate
     * @return $this
     */
    public function setDisplayFillRate($displayFillRate)
    {
        $this->displayFillRate = $displayFillRate;

        return $this;
    }

    /**
     * @return float
     */
    public function getDisplayIPV()
    {
        return $this->displayIPV;
    }

    /**
     * @param float $displayIPV
     * @return $this
     */
    public function setDisplayIPV($displayIPV)
    {
        $this->displayIPV = $displayIPV;

        return $this;
    }

    /**
     * @return int
     */
    public function getDisplayImpressions()
    {
        return $this->displayImpressions;
    }

    /**
     * @param int $displayImpressions
     * @return $this
     */
    public function setDisplayImpressions($displayImpressions)
    {
        $this->displayImpressions = $displayImpressions;

        return $this;
    }

    /**
     * @return int
     */
    public function getDisplayOpportunities()
    {
        return $this->displayOpportunities;
    }

    /**
     * @param int $displayOpportunities
     * @return $this
     */
    public function setDisplayOpportunities($displayOpportunities)
    {
        $this->displayOpportunities = $displayOpportunities;

        return $this;
    }

    /**
     * @return int
     */
    public function getVideoPlayerReady()
    {
        return $this->videoPlayerReady;
    }

    /**
     * @param int $videoPlayerReady
     * @return $this
     */
    public function setVideoPlayerReady($videoPlayerReady)
    {
        $this->videoPlayerReady = $videoPlayerReady;

        return $this;
    }

    /**
     * @return int
     */
    public function getVideoAdImpressions()
    {
        return $this->videoAdImpressions;
    }

    /**
     * @param int $videoAdImpressions
     * @return $this
     */
    public function setVideoAdImpressions($videoAdImpressions)
    {
        $this->videoAdImpressions = $videoAdImpressions;

        return $this;
    }

    /**
     * @return int
     */
    public function getVideoAdPlays()
    {
        return $this->videoAdPlays;
    }

    /**
     * @param int $videoAdPlays
     * @return $this
     */
    public function setVideoAdPlays($videoAdPlays)
    {
        $this->videoAdPlays = $videoAdPlays;

        return $this;
    }

    /**
     * @return float
     */
    public function getVideoIPV()
    {
        return $this->videoIPV;
    }

    /**
     * @param float $videoIPV
     * @return $this
     */
    public function setVideoIPV($videoIPV)
    {
        $this->videoIPV = $videoIPV;

        return $this;
    }

    /**
     * @return float
     */
    public function getVideoAdCompletionRate()
    {
        return $this->videoAdCompletionRate;
    }

    /**
     * @param float $videoAdCompletionRate
     * @return $this
     */
    public function setVideoAdCompletionRate($videoAdCompletionRate)
    {
        $this->videoAdCompletionRate = $videoAdCompletionRate;

        return $this;
    }

    /**
     * @return int
     */
    public function getVideoAdCompletions()
    {
        return $this->videoAdCompletions;
    }

    /**
     * @param int $videoAdCompletions
     * @return $this
     */
    public function setVideoAdCompletions($videoAdCompletions)
    {
        $this->videoAdCompletions = $videoAdCompletions;

        return $this;
    }

    /**
     * @return int
     */
    public function getVideoAdClicks()
    {
        return $this->videoAdClicks;
    }

    /**
     * @param int $videoAdClicks
     * @return $this
     */
    public function setVideoAdClicks($videoAdClicks)
    {
        $this->videoAdClicks = $videoAdClicks;

        return $this;
    }

    /**
     * @return int
     */
    public function getVideoStarts()
    {
        return $this->videoStarts;
    }

    /**
     * @param int $videoStarts
     * @return $this
     */
    public function setVideoStarts($videoStarts)
    {
        $this->videoStarts = $videoStarts;

        return $this;
    }

    /**
     * @return int
     */
    public function getVideoEnds()
    {
        return $this->videoEnds;
    }

    /**
     * @param int $videoEnds
     * @return $this
     */
    public function setVideoEnds($videoEnds)
    {
        $this->videoEnds = $videoEnds;

        return $this;
    }

    /**
     * @return int
     */
    public function getVisits()
    {
        return $this->visits;
    }

    /**
     * @param int $visits
     * @return $this
     */
    public function setVisits($visits)
    {
        $this->visits = $visits;

        return $this;
    }

    /**
     * @return int
     */
    public function getPageViews()
    {
        return $this->pageViews;
    }

    /**
     * @param int $pageViews
     * @return $this
     */
    public function setPageViews($pageViews)
    {
        $this->pageViews = $pageViews;

        return $this;
    }

    /**
     * QTOS = Quality Time on Site
     *
     * @return int
     */
    public function getQtos()
    {
        return $this->qtos;
    }

    /**
     * QTOS = Quality Time on Site
     *
     * @param int $qtos
     * @return $this
     */
    public function setQtos($qtos)
    {
        $this->qtos = $qtos;

        return $this;
    }

    /**
     * @return float
     */
    public function getQtosPercentage()
    {
        return $this->qtosPercentage;
    }

    /**
     * @param float $qtosPercentage
     * @return $this
     */
    public function setQtosPercentage($qtosPercentage)
    {
        $this->qtosPercentage = $qtosPercentage;

        return $this;
    }
}