<?php

namespace Tagcade\Service\Report\SourceReport;

use Tagcade\Domain\DTO\Report\SourceReport\ReportGroup;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Report\SourceReport\Report as ReportModel;
use Tagcade\Model\Report\CalculateRatiosTrait;

class ReportGrouper
{
    use CalculateRatiosTrait;

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
     * @var int
     */
    protected $averageVisits;

    /**
     * @var int
     */
    protected $averagePageViews;

    /**
     * @var float
     */
    protected $qtosPercentage;

    public function __construct(array $reports) {
        foreach($reports as $report) {
            if (!$report instanceof ReportModel) {
                throw new InvalidArgumentException('Expected only source report models');
            }
        }

        $this->groupReports($reports);
    }

    public function getGroupedReport()
    {
        return new ReportGroup(
            $this->displayOpportunities,
            $this->displayImpressions,
            $this->displayFillRate,
            $this->displayClicks,
            $this->displayCTR,
            $this->displayIPV,
            $this->videoPlayerReady,
            $this->videoAdPlays,
            $this->videoAdImpressions,
            $this->videoAdCompletions,
            $this->videoAdCompletionRate,
            $this->videoIPV,
            $this->videoAdClicks,
            $this->videoStarts,
            $this->videoEnds,
            $this->visits,
            $this->pageViews,
            $this->qtos,
            $this->qtosPercentage,
            $this->averageVisits,
            $this->averagePageViews,
            $this->getRatio($this->pageViews, $this->visits)
        );
    }

    /**
     * @return int
     */
    public function getAveragePageViews()
    {
        return $this->averagePageViews;
    }

    /**
     * @return int
     */
    public function getAverageVisits()
    {
        return $this->averageVisits;
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
     * @param ReportModel[] $reports
     */
    protected function groupReports(array $reports)
    {
        foreach($reports as $report) {
            $this
                ->addDisplayOpportunities($report->getDisplayOpportunities())
                ->addDisplayImpressions($report->getDisplayImpressions())
                ->addDisplayClicks($report->getDisplayClicks())
                ->addVideoPlayerReady($report->getVideoPlayerReady())
                ->addVideoAdPlays($report->getVideoAdPlays())
                ->addVideoAdImpressions($report->getVideoAdImpressions())
                ->addVideoAdCompletions($report->getVideoAdCompletions())
                ->addVideoAdClicks($report->getVideoAdClicks())
                ->addVideoStarts($report->getVideoStarts())
                ->addVideoEnds($report->getVideoEnds())
                ->addVisits($report->getVisits())
                ->addPageViews($report->getPageViews())
                ->addQtos($report->getQtos())
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

        $reportCount = count($reports);

        $this->averageVisits = $this->getRatio($this->getVisits(), $reportCount);
        $this->averagePageViews = $this->getRatio($this->getPageViews(), $reportCount);
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