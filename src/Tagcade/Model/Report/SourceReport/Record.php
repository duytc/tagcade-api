<?php

namespace Tagcade\Model\Report\SourceReport;

use Doctrine\Common\Collections\ArrayCollection;
use Tagcade\Model\Report\CalculateRatiosTrait;

class Record
{
    use CalculateRatiosTrait;

    protected $id;

    /**
     * @var Report
     */
    protected $sourceReport;

    /**
     * @var string
     */
    protected $embeddedTrackingKeys;

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
        $this->trackingKeys = new ArrayCollection();
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
        $this->displayOpportunities = (int) $displayOpportunities;

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
        $this->displayImpressions = (int) $displayImpressions;

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
     * @return $this
     */
    public function setDisplayFillRate()
    {
        $this->displayFillRate = $this->getPercentage($this->displayImpressions, $this->displayOpportunities);

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
        $this->displayClicks = (int) $displayClicks;

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
     * @return $this
     */
    public function setDisplayCTR()
    {
        $this->displayCTR = $this->getPercentage($this->displayClicks, $this->displayImpressions);

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
     * @return $this
     */
    public function setDisplayIPV()
    {
        $this->displayIPV = $this->getRatio($this->displayImpressions, $this->visits);

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
        $this->videoPlayerReady = (int) $videoPlayerReady;

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
        $this->videoAdPlays = (int) $videoAdPlays;

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
    public function setVideoAdImpressions($videoAdImpressions = null)
    {
        $this->videoAdImpressions = (int) $videoAdImpressions;

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
        $this->videoAdCompletions = (int) $videoAdCompletions;

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
     * @return $this
     */
    public function setVideoAdCompletionRate()
    {
        $this->videoAdCompletionRate = $this->getPercentage($this->videoAdCompletions, $this->videoAdPlays);

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
     * @return $this
     */
    public function setVideoIPV()
    {
        $this->videoIPV = $this->getRatio($this->videoAdImpressions, $this->visits);

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
        $this->videoAdClicks = (int) $videoAdClicks;

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
        $this->videoStarts = (int) $videoStarts;

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
        $this->videoEnds = (int) $videoEnds;

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
        $this->visits = (int) $visits;

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
        $this->pageViews = (int) $pageViews;

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
        $this->qtos = (int) $qtos;

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
     * @return $this
     */
    public function setQtosPercentage()
    {
        $this->qtosPercentage = $this->getPercentage($this->qtos, $this->pageViews);

        return $this;
    }

    /**
     * @return array|null
     */
    public function getTrackingKeyTerms()
    {
        if (null == $this->embeddedTrackingKeys) {
            $this->setEmbeddedTrackingKeys();

            if (!is_array($this->embeddedTrackingKeys)) {
                return null;
            }
        }

        return array_keys($this->embeddedTrackingKeys);
    }

    public function setCalculatedFields()
    {
        $this->setEmbeddedTrackingKeys();

        if (null == $this->videoAdImpressions && is_numeric($this->videoAdPlays)) {
            $this->setVideoAdImpressions($this->videoAdPlays);
        }

        $this->setDisplayIPV();
        $this->setVideoIPV();

        $this->setDisplayFillRate();
        $this->setDisplayCTR();
        $this->setVideoAdCompletionRate();
        $this->setQtosPercentage();
    }

    protected function setEmbeddedTrackingKeys()
    {
        if ($this->trackingKeys->isEmpty()) {
            $this->embeddedTrackingKeys = null;
            return;
        }

        $embeddedTrackingKeys = [];

        foreach($this->trackingKeys as $trackingKey) {
            /** @var TrackingKey $trackingKey */
            $embeddedTrackingKeys[$trackingKey->getTrackingTerm()->getTerm()] = $trackingKey->getValue();
        }

        $this->embeddedTrackingKeys = $embeddedTrackingKeys;
    }
}