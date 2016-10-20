<?php


namespace Tagcade\Service\Report\SourceReport\Selector\Grouper\Groupers;


use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Report\CalculateRatiosTrait;
use Tagcade\Model\Report\PerformanceReport\CalculateWeightedValueTrait;
use Tagcade\Model\Report\SourceReport\ReportInterface;
use Tagcade\Service\Report\SourceReport\Result\Group\ReportGroup;
use Tagcade\Service\Report\SourceReport\Result\ReportResultInterface;

abstract class AbstractGrouper implements GrouperInterface
{
    use CalculateRatiosTrait;
    use CalculateWeightedValueTrait;

    private $reports;
    private $reportName;
    private $startDate;
    private $endDate;

    private $displayOpportunities;
    private $displayImpressions;
    private $displayFillRate;
    private $displayClicks;
    private $displayCTR;
    private $displayIPV;
    private $videoPlayerReady;
    private $videoAdPlays;
    private $videoAdImpressions;
    private $videoAdCompletions;
    private $videoAdCompletionRate;
    private $videoIPV;
    private $videoAdClicks;
    private $videoStarts;
    private $videoEnds;
    private $visits;
    private $pageViews;
    private $qtos;
    private $qtosPercentage;
    private $billedRate;
    private $billedAmount;

    private $averageVisits;
    private $averagePageViews;
    private $averageBilledAmount;

    /**
     * @param ReportResultInterface $reportResult
     */
    public function __construct(ReportResultInterface $reportResult)
    {
        $reports = $reportResult->getReports();

        if (empty($reports)) {
            throw new InvalidArgumentException('Expected a non-empty array of reports');
        }

        $this->reportName = $reportResult->getName();
        $this->startDate = $reportResult->getStartDate();
        $this->endDate = $reportResult->getEndDate();
        $this->reports = $reports;

        $this->groupReports($reports);
    }

    /**
     * @param ReportInterface[] $reports
     */
    protected function groupReports(array $reports)
    {
        foreach ($reports as $report) {
            $this->doGroupReport($report);
        }

        $this->billedRate = $this->calculateWeightedValue($reports, $frequency = 'billedRate', $weight = 'billedAmount');

        // Calculate average for totalOpportunities,impressions and passbacks
        $reportCount = count($this->getReports());
        $this->averagePageViews = $this->getRatio($this->getPageViews(), $reportCount);
        $this->averageVisits = $this->getRatio($this->getVisits(), $reportCount);
        $this->averageBilledAmount = $this->getRatio($this->getBilledAmount(), $reportCount);
    }

    public function getGroupedReport()
    {
        return new ReportGroup(
            $this->getReports(),
            $this->getReportName(),
            $this->getStartDate(),
            $this->getEndDate(),
            $this->getDisplayOpportunities(),
            $this->getDisplayImpressions(),
            $this->getDisplayFillRate(),
            $this->getDisplayClicks(),
            $this->getDisplayCTR(),
            $this->getDisplayIPV(),
            $this->getVideoPlayerReady(),
            $this->getVideoAdPlays(),
            $this->getVideoAdImpressions(),
            $this->getVideoAdCompletions(),
            $this->getVideoAdCompletionRate(),
            $this->getVideoIPV(),
            $this->getVideoAdClicks(),
            $this->getVideoStarts(),
            $this->getVideoEnds(),
            $this->getVisits(),
            $this->getPageViews(),
            $this->getQtos(),
            $this->getQtosPercentage(),
            $this->getBilledRate(),
            $this->getBilledAmount(),
            $this->getAverageVisits(),
            $this->getAveragePageViews(),
            $this->getAverageBilledAmount()
        );
    }

    protected function doGroupReport(ReportInterface $report)
    {
        $this->addDisplayOpportunities($report->getDisplayOpportunities())
            ->addDisplayImpression($report->getDisplayImpressions())
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
            ->addBilledAmount($report->getBilledAmount());

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
        $this->displayOpportunities += $displayOpportunities;
        return $this;
    }

    protected function addDisplayImpression($displayImpressions)
    {
        $this->displayImpressions += $displayImpressions;
        return $this;
    }

    protected function addDisplayClicks($displayClicks)
    {
        $this->displayClicks += $displayClicks;
        return $this;
    }

    protected function addVideoPlayerReady($videoPlayerReady)
    {
        $this->videoPlayerReady += $videoPlayerReady;
        return $this;
    }

    protected function addVideoAdPlays($videoAdPlays)
    {
        $this->videoAdPlays += $videoAdPlays;
        return $this;
    }

    protected function addVideoAdImpressions($videoImpressions)
    {
        $this->videoAdImpressions += $videoImpressions;
        return $this;
    }

    protected function addVideoAdCompletions($videoAdCompletions)
    {
        $this->videoAdCompletions += $videoAdCompletions;
        return $this;
    }

    protected function addVideoAdClicks($videoAdClicks)
    {
        $this->videoAdClicks += $videoAdClicks;
        return $this;
    }

    protected function addVideoStarts($videoStarts)
    {
        $this->videoStarts += $videoStarts;
        return $this;
    }

    protected function addVideoEnds($videoEnds)
    {
        $this->videoEnds += $videoEnds;
        return $this;
    }

    protected function addVisits($visits)
    {
        $this->visits += $visits;
        return $this;
    }

    protected function addPageViews($pageViews)
    {
        $this->pageViews += $pageViews;
        return $this;
    }


    protected function addQtos($qTos)
    {
        $this->qtos += $qTos;
        return $this;
    }

    protected function addBilledAmount($billedAmount)
    {
        $this->billedAmount += $billedAmount;
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

    /**
     * @return \Tagcade\Model\Report\SourceReport\ReportInterface[]
     */
    public function getReports()
    {
        return $this->reports;
    }

    /**
     * @return null|string
     */
    public function getReportName()
    {
        return $this->reportName;
    }

    /**
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @return \DateTime
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
     * @return mixed
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
     * @return mixed
     */
    public function getBilledRate()
    {
        return $this->billedRate;
    }

    /**
     * @return mixed
     */
    public function getBilledAmount()
    {
        return $this->billedAmount;
    }

    /**
     * @return mixed
     */
    public function getAverageVisits()
    {
        return $this->averageVisits;
    }

    /**
     * @return mixed
     */
    public function getAveragePageViews()
    {
        return $this->averagePageViews;
    }

    /**
     * @return mixed
     */
    public function getAverageBilledAmount()
    {
        return $this->averageBilledAmount;
    }
}