<?php

namespace Tagcade\Entity\Report\SourceReport;

use Doctrine\Common\Collections\ArrayCollection;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="source_report_records")
 */
class Record
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     * @var int
     **/
    protected $id;
    /**
     * @ORM\ManyToOne(targetEntity="Report", inversedBy="records")
     */
    protected $sourceReport;
    /**
     * @ORM\ManyToMany(targetEntity="TrackingKey", cascade={"persist", "remove"})
     * @ORM\JoinTable(name="source_report_record_tracking_keys")
     */
    protected $trackingKeys;
    /**
     * @ORM\Column(type="integer")
     * @var int
     */
    protected $displayOpportunities;
    /**
     * @ORM\Column(type="integer")
     * @var int
     */
    protected $displayImpressions;
    /**
     * @ORM\Column(type="decimal", precision=0, scale=4)
     * @var float
     */
    protected $displayFillRate;
    /**
     * @ORM\Column(type="integer")
     * @var int
     */
    protected $displayClicks;
    /**
     * @ORM\Column(type="decimal", precision=0, scale=4)
     * @var float
     */
    protected $displayCTR;
    /**
     * @ORM\Column(type="decimal", precision=0, scale=4)
     * @var float
     */
    protected $displayIPV;
    /**
     * @ORM\Column(type="integer")
     * @var int
     */
    protected $videoPlayerReady;
    /**
     * @ORM\Column(type="integer")
     * @var int
     */
    protected $videoAdPlays;
    /**
     * @ORM\Column(type="integer")
     * @var int
     */
    protected $videoAdImpressions;
    /**
     * @ORM\Column(type="integer")
     * @var int
     */
    protected $videoAdCompletions;
    /**
     * @ORM\Column(type="decimal", precision=0, scale=4)
     * @var float
     */
    protected $videoAdCompletionRate;
    /**
     * @ORM\Column(type="decimal", precision=0, scale=4)
     * @var float
     */
    protected $videoIPV;
    /**
     * @ORM\Column(type="integer")
     * @var int
     */
    protected $videoAdClicks;
    /**
     * @ORM\Column(type="integer")
     * @var int
     */
    protected $videoStarts;
    /**
     * @ORM\Column(type="integer")
     * @var int
     */
    protected $videoEnds;
    /**
     * @ORM\Column(type="integer")
     * @var int
     */
    protected $visits;
    /**
     * @ORM\Column(type="integer")
     * @var int
     */
    protected $pageViews;
    /**
     * @ORM\Column(type="integer")
     * @var int
     */
    protected $qtos;
    /**
     * @ORM\Column(type="decimal", precision=0, scale=4)
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
     */
    public function setSourceReport(Report $sourceReport)
    {
        $this->sourceReport = $sourceReport;
    }

    /**
     * @return ArrayCollection
     */
    public function getTrackingKeys()
    {
        return $this->trackingKeys;
    }

    /**
     * @param mixed $trackingKeys
     */
    public function addTrackingKey(TrackingKey $trackingKey)
    {
        $this->trackingKeys[] = $trackingKey;
    }

    /**
     * @param float $displayCTR
     */
    public function setDisplayCTR($displayCTR)
    {
        $this->displayCTR = $displayCTR;
    }

    /**
     * @return float
     */
    public function getDisplayCTR()
    {
        return $this->displayCTR;
    }

    /**
     * @param int $displayClicks
     */
    public function setDisplayClicks($displayClicks)
    {
        $this->displayClicks = $displayClicks;
    }

    /**
     * @return int
     */
    public function getDisplayClicks()
    {
        return $this->displayClicks;
    }

    /**
     * @param float $displayFillRate
     */
    public function setDisplayFillRate($displayFillRate)
    {
        $this->displayFillRate = $displayFillRate;
    }

    /**
     * @return float
     */
    public function getDisplayFillRate()
    {
        return $this->displayFillRate;
    }

    /**
     * @param float $displayIPV
     */
    public function setDisplayIPV($displayIPV)
    {
        $this->displayIPV = $displayIPV;
    }

    /**
     * @return float
     */
    public function getDisplayIPV()
    {
        return $this->displayIPV;
    }

    /**
     * @param int $displayImpressions
     */
    public function setDisplayImpressions($displayImpressions)
    {
        $this->displayImpressions = $displayImpressions;
    }

    /**
     * @return int
     */
    public function getDisplayImpressions()
    {
        return $this->displayImpressions;
    }

    /**
     * @param int $displayOpportunities
     */
    public function setDisplayOpportunities($displayOpportunities)
    {
        $this->displayOpportunities = $displayOpportunities;
    }

    /**
     * @return int
     */
    public function getDisplayOpportunities()
    {
        return $this->displayOpportunities;
    }

    /**
     * @param int $pageViews
     */
    public function setPageViews($pageViews)
    {
        $this->pageViews = $pageViews;
    }

    /**
     * @return int
     */
    public function getPageViews()
    {
        return $this->pageViews;
    }

    /**
     * @param int $qtos
     */
    public function setQtos($qtos)
    {
        $this->qtos = $qtos;
    }

    /**
     * @return int
     */
    public function getQtos()
    {
        return $this->qtos;
    }

    /**
     * @param float $qtosPercentage
     */
    public function setQtosPercentage($qtosPercentage)
    {
        $this->qtosPercentage = $qtosPercentage;
    }

    /**
     * @return float
     */
    public function getQtosPercentage()
    {
        return $this->qtosPercentage;
    }

    /**
     * @param float $videoAdCompletionRate
     */
    public function setVideoAdCompletionRate($videoAdCompletionRate)
    {
        $this->videoAdCompletionRate = $videoAdCompletionRate;
    }

    /**
     * @return float
     */
    public function getVideoAdCompletionRate()
    {
        return $this->videoAdCompletionRate;
    }

    /**
     * @param int $videoAdCompletions
     */
    public function setVideoAdCompletions($videoAdCompletions)
    {
        $this->videoAdCompletions = $videoAdCompletions;
    }

    /**
     * @return int
     */
    public function getVideoAdCompletions()
    {
        return $this->videoAdCompletions;
    }

    /**
     * @param int $videoAdPlays
     */
    public function setVideoAdPlays($videoAdPlays)
    {
        $this->videoAdPlays = $videoAdPlays;
    }

    /**
     * @return int
     */
    public function getVideoAdPlays()
    {
        return $this->videoAdPlays;
    }

    /**
     * @param float $videoIPV
     */
    public function setVideoIPV($videoIPV)
    {
        $this->videoIPV = $videoIPV;
    }

    /**
     * @return float
     */
    public function getVideoIPV()
    {
        return $this->videoIPV;
    }

    /**
     * @param int $visits
     */
    public function setVisits($visits)
    {
        $this->visits = $visits;
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
    public function getVideoPlayerReady()
    {
        return $this->videoPlayerReady;
    }

    /**
     * @param int $videoPlayerReady
     */
    public function setVideoPlayerReady($videoPlayerReady)
    {
        $this->videoPlayerReady = $videoPlayerReady;
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
     */
    public function setVideoAdImpressions($videoAdImpressions)
    {
        $this->videoAdImpressions = $videoAdImpressions;
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
     */
    public function setVideoAdClicks($videoAdClicks)
    {
        $this->videoAdClicks = $videoAdClicks;
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
     */
    public function setVideoStarts($videoStarts)
    {
        $this->videoStarts = $videoStarts;
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
     */
    public function setVideoEnds($videoEnds)
    {
        $this->videoEnds = $videoEnds;
    }
}