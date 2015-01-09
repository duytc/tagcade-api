<?php

namespace Tagcade\Model\Report\SourceReport;

use Doctrine\Common\Collections\ArrayCollection;
use \DateTime;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\Report\CalculateRatiosTrait;

class Report
{
    use CalculateRatiosTrait;

    protected $id;
    /**
     * @var DateTime
     */
    protected $date;

    /**
     * @var SiteInterface
     */
    protected $site;

    /**
     * @var array
     */
    protected $records;

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
        $this->records = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param DateTime $date
     * @return $this
     */
    public function setDate(DateTime $date)
    {
        $this->date = $date;

        return $this;
    }

    public function getSite()
    {
        return $this->site;
    }

    /**
     * @param SiteInterface $site
     * @return $this
     */
    public function setSite($site)
    {
        $this->site = $site;

        return $this;
    }
    /**
     * @return int|null
     */
    public function getSiteId()
    {
        if ($this->site instanceof SiteInterface) {
            return $this->site->getId();
        }

        return null;
    }


    public function getRecords()
    {
        return $this->records;
    }

    /**
     * @param Record $item
     * @return $this
     */
    public function addRecord(Record $item)
    {
        $item->setSourceReport($this);
        $this->records[] = $item;

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
     * @return int
     */
    public function getDisplayImpressions()
    {
        return $this->displayImpressions;
    }

    /**
     * @return float
     */
    public function getDisplayFillRate()
    {
        return $this->displayFillRate;
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
    public function getDisplayCTR()
    {
        return $this->displayCTR;
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
    public function getVideoPlayerReady()
    {
        return $this->videoPlayerReady;
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
    public function getVideoAdImpressions()
    {
        return $this->videoAdImpressions;
    }

    /**
     * @return int
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
     * @return float
     */
    public function getVideoIPV()
    {
        return $this->videoIPV;
    }

    /**
     * @return int
     */
    public function getVideoAdClicks()
    {
        return $this->videoAdClicks;
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
    public function getVideoEnds()
    {
        return $this->videoEnds;
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
    public function getPageViews()
    {
        return $this->pageViews;
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
     * @return float
     */
    public function getQtosPercentage()
    {
        return $this->qtosPercentage;
    }

    public function setCalculatedFields()
    {
        $this->displayOpportunities = null;
        $this->displayImpressions = null;
        $this->displayClicks = null;
        $this->videoPlayerReady = null;
        $this->videoAdPlays = null;
        $this->videoAdImpressions = null;
        $this->videoAdCompletions = null;
        $this->videoAdClicks = null;
        $this->videoStarts = null;
        $this->videoEnds = null;
        $this->visits = null;
        $this->pageViews = null;
        $this->qtos = null;

        foreach($this->records as $record) {
            /** @var Record $record */
            $this
                ->addDisplayOpportunities($record->getDisplayOpportunities())
                ->addDisplayImpressions($record->getDisplayImpressions())
                ->addDisplayClicks($record->getDisplayClicks())
                ->addVideoPlayerReady($record->getVideoPlayerReady())
                ->addVideoAdPlays($record->getVideoAdPlays())
                ->addVideoAdImpressions($record->getVideoAdImpressions())
                ->addVideoAdCompletions($record->getVideoAdCompletions())
                ->addVideoAdClicks($record->getVideoAdClicks())
                ->addVideoStarts($record->getVideoStarts())
                ->addVideoEnds($record->getVideoEnds())
                ->addVisits($record->getVisits())
                ->addPageViews($record->getPageViews())
                ->addQtos($record->getQtos())
            ;
        }

        $this
            ->setDisplayFillRate()
            ->setDisplayCTR()
            ->setDisplayIPV()
            ->setVideoIPV()
            ->setVideoAdCompletionRate()
            ->setQtosPercentage()
        ;
    }

    protected function addDisplayOpportunities($displayOpportunities)
    {
        if (is_numeric($displayOpportunities)) {
            $this->displayOpportunities += $displayOpportunities;
        }

        return $this;
    }

    protected function addDisplayImpressions($displayImpressions)
    {
        if (is_numeric($displayImpressions)) {
            $this->displayImpressions += $displayImpressions;
        }

        return $this;
    }

    protected function addDisplayClicks($displayClicks)
    {
        if (is_numeric($displayClicks)) {
            $this->displayClicks += $displayClicks;
        }

        return $this;
    }

    protected function addVideoPlayerReady($videoPlayerReady)
    {
        if (is_numeric($videoPlayerReady)) {
            $this->videoPlayerReady += $videoPlayerReady;
        }

        return $this;
    }

    protected function addVideoAdPlays($videoAdPlays)
    {
        if (is_numeric($videoAdPlays)) {
            $this->videoAdPlays += $videoAdPlays;
        }

        return $this;
    }

    protected function addVideoAdImpressions($videoAdImpressions)
    {
        if (is_numeric($videoAdImpressions)) {
            $this->videoAdImpressions += $videoAdImpressions;
        }

        return $this;
    }

    protected function addVideoAdCompletions($videoAdCompletions)
    {
        if (is_numeric($videoAdCompletions)) {
            $this->videoAdCompletions += $videoAdCompletions;
        }

        return $this;
    }

    protected function addVideoAdClicks($videoAdClicks)
    {
        if (is_numeric($videoAdClicks)) {
            $this->videoAdClicks += $videoAdClicks;
        }

        return $this;
    }

    protected function addVideoStarts($videoStarts)
    {
        if (is_numeric($videoStarts)) {
            $this->videoStarts += $videoStarts;
        }

        return $this;
    }

    protected function addVideoEnds($videoEnds)
    {
        if (is_numeric($videoEnds)) {
            $this->videoEnds += $videoEnds;
        }

        return $this;
    }

    protected function addVisits($visits)
    {
        if (is_numeric($visits)) {
            $this->visits += $visits;
        }

        return $this;
    }

    protected function addPageViews($pageViews)
    {
        if (is_numeric($pageViews)) {
            $this->pageViews += $pageViews;
        }

        return $this;
    }

    protected function addQtos($qtos)
    {
        if (is_numeric($qtos)) {
            $this->qtos += $qtos;
        }

        return $this;
    }

    protected function setDisplayFillRate()
    {
        $this->displayFillRate = $this->getPercentage($this->displayImpressions, $this->displayOpportunities);

        return $this;
    }

    protected function setDisplayCTR()
    {
        $this->displayCTR = $this->getPercentage($this->displayClicks, $this->displayImpressions);

        return $this;
    }

    protected function setDisplayIPV()
    {
        $this->displayIPV = $this->getRatio($this->displayImpressions, $this->visits);

        return $this;
    }

    protected function setVideoIPV()
    {
        $this->videoIPV = $this->getRatio($this->videoAdImpressions, $this->visits);

        return $this;
    }

    protected function setVideoAdCompletionRate()
    {
        $this->videoAdCompletionRate = $this->getPercentage($this->videoAdCompletions, $this->videoAdPlays);

        return $this;
    }

    protected function setQtosPercentage()
    {
        $this->qtosPercentage = $this->getPercentage($this->qtos, $this->pageViews);

        return $this;
    }
}