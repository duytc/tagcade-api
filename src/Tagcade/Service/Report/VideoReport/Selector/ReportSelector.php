<?php


namespace Tagcade\Service\Report\VideoReport\Selector;


use DateTime;
use Symfony\Component\Validator\Constraints\Date;
use Tagcade\Model\Report\VideoReport\ReportType\ReportTypeInterface;
use Tagcade\Service\DateUtil;
use Tagcade\Service\DateUtilInterface;
use Tagcade\Service\Report\VideoReport\Creator\ReportCreatorInterface;
use Tagcade\Service\Report\VideoReport\Creator\VideoReportCreatorInterface;
use Tagcade\Service\Report\VideoReport\Parameter\BreakDownParameterInterface;
use Tagcade\Service\Report\VideoReport\Parameter\FilterParameterInterface;
use Tagcade\Service\Report\VideoReport\Selector\Grouper\VideoReportGrouperInterface;
use Tagcade\Service\Report\VideoReport\Selector\Result\ReportCollection;
use Tagcade\Service\Report\VideoReport\Selector\Selectors\Hierarchy\SelectorInterface;
use Tagcade\Service\StringUtilTrait;

class ReportSelector implements ReportSelectorInterface
{
    use StringUtilTrait;
    /**
     * @var array
     */
    private $selectors;
    /**
     * @var VideoReportTransformerInterface
     */
    private $videoReportTransformer;
    /**
     * @var VideoReportGrouperInterface
     */
    private $videoReportGrouper;
    /**
     * @var DateUtilInterface
     */
    private $dateUtil;
    /**
     * @var ReportCreatorInterface
     */
    private $videoReportCreator;

    function __construct(array $selectors, VideoReportTransformerInterface $videoReportTransformer, VideoReportGrouperInterface $videoReportGrouper,
            DateUtilInterface $dateUtil, $videoReportCreator = null)
    {
        $this->selectors = $selectors;
        $this->videoReportTransformer = $videoReportTransformer;
        $this->videoReportGrouper = $videoReportGrouper;
        $this->dateUtil = $dateUtil;
        $this->videoReportCreator = $videoReportCreator;
    }

    /**
     * @inheritdoc
     */
    public function getReport(ReportTypeInterface $reportType, FilterParameterInterface $filterParameter, BreakDownParameterInterface $breakDownParameter)
    {
        // 1. get reports due to reportType
        $resultReport = $this->getRawReport($reportType, $filterParameter);

        if (empty($resultReport)) {
            return false;
        }

        $reports[$reportType->getVideoObjectId()] = $resultReport;
        $reportsAggregateByParentId = $reports;

        // 2. transform report due to breakDownParameter,
        // e.g transform from VideoDemandAdTagReports to VideoWaterfallTagReports because user need breakdown by VideoWaterfallTag
        $isTransform = $this->needTransformReports($breakDownParameter, $reportType);
        if (true == $isTransform) {
            $reportsResult = $this->videoReportTransformer->transformReport($reports, $reportType, $breakDownParameter,$filterParameter);

            $reports = $reportsResult['reports'];
            $reportTypes = $reportsResult['reportType'];

            foreach ($reports as $key => $parentReport) {
                // get parent id form: 'parentId:y-m-d'
                $parentId =  $this->extractParentId($key);
                if (!array_key_exists($parentId, $reportsAggregateByParentId)) {
                    $reportsAggregateByParentId[$parentId] = [];
                }
                $reportsAggregateByParentId[$parentId][] = $parentReport;
            }
        }

        //Group by date range if need
        if (!$breakDownParameter->hasDay()) {
            $resultReports = [];
            foreach ($reportsAggregateByParentId as $reportOfOneId) {
                $reportCollection = new ReportCollection($reportType, $reportOfOneId, $filterParameter->getStartDate(), $filterParameter->getEndDate());
                $parentReport = $this->videoReportGrouper->groupReports($reportCollection);
                $parentReport->setReports(null); // To reduce size returned data
                $resultReports[] = $parentReport;

            }

            $reports = $resultReports;
        } else {
            unset($reports);
            foreach ($reportsAggregateByParentId as $reportOfOneId) {
                foreach ($reportOfOneId as $report) {
                    $reports[] = $report;
                }
            }
        }

        // 3. create reportCollection: check if need using actualStartDate/EndDate, reportName
        $reportCollection = new ReportCollection($reportType, $reports, $filterParameter->getStartDate(),$filterParameter->getEndDate());

        // 4. group reports if needed
        $isGroup = $this->needGroupReports($breakDownParameter);
        if (true == $isGroup) {
            return $this->videoReportGrouper->groupReports($reportCollection);
        }

        // 4'. return reports if not need do grouping
        return $reportCollection;
    }

    /**
     * @inheritdoc
     */
    public function getMultipleReports(array $reportTypes, FilterParameterInterface $filterParameter, BreakDownParameterInterface $breakDownParameter)
    {
        $reports = [];

        /**@var ReportTypeInterface $reportType */
        foreach ($reportTypes as $reportType) {
            if ($reportResult = $this->getRawReport($reportType, $filterParameter)) {
                $reports[$reportType->getVideoObjectId()] = $reportResult;
            }
            unset($reportResult);
        }

        if (empty($reports)) {
            return false;
        }

        $reportsAggregateByParentId = $reports;
        $reportType = reset($reportTypes);

        // 2. transform report due to breakDownParameter,
        // e.g transform from VideoDemandAdTagReports to VideoWaterfallTagReports because user need breakdown by VideoWaterfallTag
        $isTransform = $this->needTransformReports($breakDownParameter, $reportType);
        if (true == $isTransform) {
            $reportsForTransform = call_user_func_array('array_merge', $reports);
            $reportsResult = $this->videoReportTransformer->transformReport($reportsForTransform, $reportType, $breakDownParameter, $filterParameter);

            $reports = $reportsResult['reports'];
            $reportTypes = $reportsResult['reportType'];

            $reportsAggregateByParentId = [];
            foreach ($reports as $key => $parentReport) {
                // get parent id form: 'parentId:y-m-d'
                $parentId =  $this->extractParentId($key);
                if (!array_key_exists($parentId, $reportsAggregateByParentId)) {
                    $reportsAggregateByParentId[$parentId] = [];
                }
                $reportsAggregateByParentId[$parentId][] = $parentReport;
            }
        }

        //Group by date range if need
        if (!$breakDownParameter->hasDay()) {
            $resultReports = [];
            $reportType = reset($reportTypes);
            foreach ($reportsAggregateByParentId as $reportOfOneId) {
                $reportCollection = new ReportCollection($reportType, $reportOfOneId, $filterParameter->getStartDate(), $filterParameter->getEndDate());
                $parentReport = $this->videoReportGrouper->groupReports($reportCollection);
                $parentReport->setReports(null); // To reduce size returned data
                $resultReports[] = $parentReport;
                $reportType = next($reportTypes);
            }

            $reports = $resultReports;
        } else {
            unset($reports);
            foreach ($reportsAggregateByParentId as $reportOfOneId) {
                foreach ($reportOfOneId as $report) {
                    $reports[] = $report;
                }
            }
        }

        // 3. create reportCollection: check if need using actualStartDate/EndDate, reportName
        $reportType = reset($reportTypes);
        $reportCollection = new ReportCollection($reportType, $reports, $filterParameter->getStartDate(), $filterParameter->getEndDate());

        // 4. group reports if needed
        $isGroup = $this->needGroupReports($breakDownParameter);
        if (true == $isGroup) {
            return $this->videoReportGrouper->groupReports($reportCollection);
        }

        // 4'. return reports if not need do grouping
        return $reportCollection;
    }

    /**
     * @param ReportTypeInterface $reportType
     * @param FilterParameterInterface $filterParameter
     * @return mixed
     * @throws \Exception
     */
    protected function getRawReport(ReportTypeInterface $reportType, FilterParameterInterface $filterParameter)
    {
        /* get selector due to reportType, also get filtered entity for reportType */
        $selector = $this->getSelectorFor($reportType);

        $todayIncludedInDateRange = $this->dateUtil->isTodayInRange($filterParameter->getStartDate(), $filterParameter->getEndDate());

        $reports = [];

        if ($todayIncludedInDateRange && $this->videoReportCreator instanceof ReportCreatorInterface) {
                $reports[] = $this->videoReportCreator->getReport($reportType);
        }

        if ($this->dateUtil->isDateBeforeToday($filterParameter->getStartDate())) {
            // get historical reports only if the start date is before today's date
            $historicalEndDate = $filterParameter->getEndDate();

            if ($todayIncludedInDateRange) {
                // since today is in the date range and we are building that report with the report creator
                // set the end date to yesterday to make sure we do not query for the current day
                $historicalEndDate = new DateTime('yesterday');
            }

            $historicalReports = $selector->getReports($reportType, $filterParameter);

            if ($historicalReports === null) {
                $historicalReports = [];
            }

            $reports = array_merge($reports, $historicalReports);

            unset($historicalReports, $historicalEndDate);
        }

        return $reports;
    }

    /**
     * @param ReportTypeInterface $reportType
     * @return SelectorInterface
     * @throws \Exception
     */
    protected function getSelectorFor(ReportTypeInterface $reportType)
    {
        /** @var SelectorInterface $selector */
        foreach ($this->selectors as $selector) {
            if ($selector->supportsReportType($reportType)) {
                return $selector;
            }
        }

        throw new \Exception(sprintf('Not found selector for that report type %s', $reportType->getReportType()));
    }

    /**
     * Check break down parameter to transform reports
     * @param BreakDownParameterInterface $breakDownParameter
     * @param ReportTypeInterface $reportType
     * @return bool
     */
    protected function needTransformReports(BreakDownParameterInterface $breakDownParameter, ReportTypeInterface $reportType)
    {
        return (!in_array($breakDownParameter->getMinBreakdown(), $reportType->supportMinBreakDown()) && $breakDownParameter->getMinBreakdown());
    }

    /**
     * Check break down parameter to group report
     *
     * @param BreakDownParameterInterface $breakDownParameter
     * @return bool
     */
    protected function needGroupReports(BreakDownParameterInterface $breakDownParameter)
    {
        //return !$breakDownParameter->hasDay();
        return true;
    }
}