<?php


namespace Tagcade\Service\Report\SourceReport\Selector\Grouper\Groupers;


use DateTime;
use Tagcade\Model\Report\SourceReport\ReportInterface;
use Tagcade\Service\Report\SourceReport\Result\Group\ReportGroup;

interface GrouperInterface
{
    /**
     * @return ReportGroup
     */
    public function getGroupedReport();

    /**
     * @return ReportInterface[]
     */
    public function getReports();

    /**
     * @return string
     */
    public function getReportName();

    /**
     * @return DateTime
     */
    public function getStartDate();

    /**
     * @return DateTime
     */
    public function getEndDate();

    /**
     * @return int
     */
    public function getDisplayOpportunities();

    /**
     * @return int
     */
    public function getDisplayImpressions();

    /**
     * @return float
     */
    public function getDisplayFillRate();

    /**
     * @return int
     */
    public function getDisplayClicks();

    /**
     * @return float
     */
    public function getDisplayCTR();

    /**
     * @return float
     */
    public function getDisplayIPV();

    /**
     * @return int
     */
    public function getVideoPlayerReady();

    /**
     * @return int
     */
    public function getVideoAdPlays();

    /**
     * @return int
     */
    public function getVideoAdImpressions();

    /**
     * @return int
     */
    public function getVideoAdCompletions();

    /**
     * @return float
     */
    public function getVideoAdCompletionRate();

    /**
     * @return float
     */
    public function getVideoIPV();

    /**
     * @return int
     */
    public function getVideoAdClicks();

    /**
     * @return int
     */
    public function getVideoStarts();

    /**
     * @return int
     */
    public function getVideoEnds();

    /**
     * @return int
     */
    public function getVisits();

    /**
     * @return int
     */
    public function getPageViews();

    /**
     * QTOS = Quality Time on Site
     *
     * @return int
     */
    public function getQtos();

    /**
     * @return float
     */
    public function getQtosPercentage();

    /**
     * @return float
     */
    public function getBilledRate();

    /**
     * @return float
     */
    public function getBilledAmount();
}