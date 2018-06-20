<?php


namespace Tagcade\Bundle\ReportApiBundle\Controller;


use DateTime;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tagcade\Behaviors\VideoUtilTrait;
use Tagcade\Model\Report\VideoReport\ReportInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Service\Report\VideoReport\Parameter\Parameter;
use Tagcade\Service\Report\VideoReport\Selector\Result\ReportCollection;

class VideoReportController extends FOSRestController
{
    use VideoUtilTrait;
    
    const COMPARISON_TYPE_YESTERDAY = 'yesterday';
    const COMPARISON_TYPE_DAY_OVER_DAY = 'day-over-day';
    const COMPARISON_TYPE_WEEK_OVER_WEEK = 'week-over-week';
    const COMPARISON_TYPE_MONTH_OVER_MONTH = 'month-over-month';
    const COMPARISON_TYPE_YEAR_OVER_YEAR = 'year-over-year';
    const COMPARISON_TYPE_CUSTOM = 'custom';

    static $SUPPORTED_COMPARISON_TYPES = [
        self::COMPARISON_TYPE_YESTERDAY,
        self::COMPARISON_TYPE_DAY_OVER_DAY,
        self::COMPARISON_TYPE_WEEK_OVER_WEEK,
        self::COMPARISON_TYPE_MONTH_OVER_MONTH,
        self::COMPARISON_TYPE_YEAR_OVER_YEAR,
        self::COMPARISON_TYPE_CUSTOM
    ];
    /**
     * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_PUBLISHER')")
     *
     * @Rest\QueryParam(name="filters", nullable=false)
     * @Rest\QueryParam(name="breakdowns", nullable=false)
     * @Rest\QueryParam(name="metrics", nullable=false)
     *
     * @Rest\View(
     *      serializerGroups={
     *              "demand_partner_demand_ad_tag.detail", "demand_partner_demand_partner.detail", "platform_account.detail",
     *              "platform_demand_ad_tag.detail", "platform_waterfall_tag.detail", "platform_platform.detail", "abstract_grouper.detail",
     *              "waterfall_tag_grouper.detail", "waterfall_tag_report_group.detail", "report_group.detail", "videoWaterfallTag.report", "videoWaterfallTagItem.report",
     *              "videoDemandAdTag.report", "libraryVideoDemandAdTag.report","videoDemandPartner.report", "user.report", "video_report_type_account.detail", "video_report_type_waterfall_tag.detail",
     *              "video_report_type_demand_ad_tag.detail", "video_report_type_platform.detail", "video_report_type_demand_partner.detail",
     *              "video_report_type_demand_partner_demand_ad_tag.detail", "video_report_type_demand_partner_waterfall_tag.detail",
     *              "demand_partner_demand_partner_waterfall_tag.detail", "platform_publisher_report.detail", "video_report_type_video_publisher_demand_partner.detail",
     *              "platform_publisher.detail", "video_report_type_platform_publisher.detail", "videoPublisher.report", "video_report_video_publisher_demand_partner.detail",
     *          }
     * )
     * @ApiDoc(
     *  section = "Video report",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  },
     *  parameters = {
     *      {"name"="filters", "dataType"="string", "required"=true, "description"="filter values for video report"},
     *      {"name"="breakdowns", "dataType"="string", "required"=true, "description"="break down value for video report"},
     *      {"name"="metric", "dataType"="string", "required"=true, "description"=" all column to show in video report"}
     *  }
     * )
     *
     * @return array
     */
    public function getVideoReportAction()
    {
        $params = $this->get('fos_rest.request.param_fetcher')->all();

        return $this->getReportByParams($params);
    }

    /**
     * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_PUBLISHER')")
     *
     * @Rest\Get("/comparison")
     * @Rest\QueryParam(name="currentStartDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="currentEndDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="historyStartDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="historyEndDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     *
     * @Rest\QueryParam(name="type", nullable=false)
     *
     * @Rest\View(
     *      serializerGroups={
     *              "demand_partner_demand_ad_tag.detail", "demand_partner_demand_partner.detail", "platform_account.detail",
     *              "platform_demand_ad_tag.detail", "platform_waterfall_tag.detail", "platform_platform.detail", "abstract_grouper.detail",
     *              "waterfall_tag_grouper.detail", "waterfall_tag_report_group.detail", "report_group.detail", "videoWaterfallTag.report", "videoWaterfallTagItem.report",
     *              "videoDemandAdTag.report", "libraryVideoDemandAdTag.report","videoDemandPartner.report", "user.report", "video_report_type_account.detail", "video_report_type_waterfall_tag.detail",
     *              "video_report_type_demand_ad_tag.detail", "video_report_type_platform.detail", "video_report_type_demand_partner.detail",
     *              "video_report_type_demand_partner_demand_ad_tag.detail", "video_report_type_demand_partner_waterfall_tag.detail",
     *              "demand_partner_demand_partner_waterfall_tag.detail", "platform_publisher_report.detail", "video_report_type_video_publisher_demand_partner.detail",
     *              "platform_publisher.detail", "video_report_type_platform_publisher.detail", "videoPublisher.report", "video_report_video_publisher_demand_partner.detail",
     *          }
     * )
     * @ApiDoc(
     *  section = "Video report",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  },
     *  parameters = {
     *      {"name"="type", "dataType"="string", "required"=true, "description"="comparison type for video report, such as day-over-day, week-over-week, month-over-month, year-over-year"},
     *  }
     * )
     *
     * @return array
     * @throws \Exception
     */
    public function getVideoReportComparisonAction()
    {
        $paramFetcher = $this->get('fos_rest.request.param_fetcher');
        $comparisonType = $paramFetcher->get('type');
        if (empty($comparisonType) || !in_array($comparisonType, self::$SUPPORTED_COMPARISON_TYPES)) {
            throw new BadRequestHttpException(sprintf('Do not support comparison type %s', $comparisonType));
        }

        if ($comparisonType == self::COMPARISON_TYPE_CUSTOM) {
            /* get start-end history Date based on comparison custom type */
            /* get start-end current Date based on comparison custom type */
            $startDateEndDate =  [
                'current' => [
                    'startDate' => $paramFetcher->get('currentStartDate', null),
                    'endDate' => $paramFetcher->get('currentEndDate', null)
                ],
                'history' => [
                    'startDate' => $paramFetcher->get('historyStartDate', null),
                    'endDate' => $paramFetcher->get('historyEndDate', null)
                ]
            ];
        } else {
            /* get startDate-endDate due to comparison type */
            $startDateEndDate = $this->getStartDateEndDateDueToComparisonType($comparisonType);
        }

        $params = $this->createParamsForReportComparison($startDateEndDate);
        $paramsForToday = reset($params);
        $paramsForYesterday = end($params);

        if ($comparisonType == 'day-over-day') {
            //day over day too slow.
            $currentReports = [];
        } else {
            try {
                $currentReports = $this->getReportByParams($paramsForToday);
            } catch (\Exception $e) {
                $currentReports = [];
            }
        }

        try {
            $historyReports = $this->getReportByParams($paramsForYesterday);
        } catch (\Exception $e) {
            //throw $e;
            $historyReports = [];
        }

        $result = [
            'current' => $currentReports,
            'history' => $historyReports,
            'startEndDateCurrent' => isset($startDateEndDate['current']) ? $startDateEndDate['current'] : []
        ];

        $minHour = 0;
        /* current */
        $maxHour = (new DateTime())->format('G');
        try {
            if ($comparisonType == 'day-over-day') {
                /* get hourly */
                $reportHourToday = $this->getReportByParamsHourly($paramsForToday);

                // trim left today
                $reportHourToday = $this->trimLeftDataHourly($reportHourToday);

                /* calculate min hour */
                $minHour = $this->getMinHour($reportHourToday);

                /*
                 * => expected hour range = [minHour, maxHour]
                 */

                /* filter */
                $reportHourToday = $this->filterDataHourly($reportHourToday, $minHour, $maxHour);

                /* patch missing for today, yesterday */
                $reportHourToday = $this->patchMissingDataHourly($reportHourToday, $minHour, $maxHour,  new DateTime('now'));

                /* result */
                $resultReportHourly = [
                    'reportHourToday' => $reportHourToday,
                ];

                $result = array_merge($resultReportHourly, $result);
                $result['current'] = end($reportHourToday);
            }

//            if ($comparisonType == 'day-over-day') {
//                $reportHourToday = $this->getReportByParamsHourly($paramsForToday);
//
//                //filter delete the hours that do not have data
//                $reportHourToday = $this->filterDataHourly($reportHourToday);
//                $resultReportHourly = [
//                    'reportHourToday' => $reportHourToday
//                ];
//                $result = array_merge($resultReportHourly, $result);
//                $result['current'] = end($reportHourToday);
//            }
        } catch (\Exception $e) {
            $resultReportHourly = [
                'reportHourToday' => []
            ];
            $result = array_merge($resultReportHourly, $result);
        }

        try {
            if ($comparisonType == 'day-over-day') {

                /* get hourly */
                $reportHourHistory = $this->getReportByParamsHourly($paramsForYesterday);

                /*
                 * => expected hour range = [minHour, maxHour]
                 */

                /* filter yesterday */
                $reportHourHistory = $this->filterDataHourly($reportHourHistory, $minHour, $maxHour);

                /* patch missing for today, yesterday */
                $reportHourHistory = $this->patchMissingDataHourly($reportHourHistory, $minHour, $maxHour,  new DateTime('now'));

                /* result */
                $resultReportHourly = [
                    'reportHourHistory' => $reportHourHistory,
                ];

                $result = array_merge($resultReportHourly, $result);
            }
//            if ($comparisonType == 'day-over-day') {
//                $reportHourHistory = $this->getReportByParamsHourly($paramsForYesterday);
//
//                //filter delete the hours that do not have data
//                $reportHourHistory = $this->filterDataHourly($reportHourHistory);
//
//                $resultReportHourly = [
//                    'reportHourHistory' => $reportHourHistory
//                ];
//                $result = array_merge($resultReportHourly, $result);
//            }
        } catch (\Exception $e) {
            $resultReportHourly = [
                'reportHourHistory' => []
            ];
            $result = array_merge($resultReportHourly, $result);
        }

        return $result;
    }

    /**
     * @param array $params
     * @return mixed
     */
    private function getReportByParams(array $params)
    {
        $parameterObject = new Parameter($params);
        $filterObject = $parameterObject->getFilterObject();
        $breakDownObject = $parameterObject->getBreakDownObject();

        if ($this->getUser() instanceof PublisherInterface) {
            $publisherId = $this->getUser()->getId();
            $filterObject->setPublisherId([$publisherId]);
        }

        return $this->getResult(
            $this->getReportBuilder()->getReports($filterObject, $breakDownObject)
        );
    }

    /**
     * @param array $params
     * @return mixed
     */
    private function getReportByParamsHourly(array $params)
    {
        $parameterObject = new Parameter($params);
        $filterObject = $parameterObject->getFilterObject();
        $breakDownObject = $parameterObject->getBreakDownObject();

        if ($this->getUser() instanceof PublisherInterface) {
            $publisherId = $this->getUser()->getId();
            $filterObject->setPublisherId([$publisherId]);
        }

        return $this->getResult(
            $this->getReportBuilder()->getReportsHourly($filterObject, $breakDownObject)
        );
    }
    /**
     * @return \Tagcade\Service\Report\VideoReport\Selector\VideoReportBuilder
     */
    private function getReportBuilder()
    {
        return $this->get('tagcade.service.report.video_report.selector.video_report_builder');
    }

    /**
     * get Result
     * @param $result
     * @return mixed
     * @throws NotFoundHttpException
     */
    private function getResult($result)
    {
        /** @var false|array|ReportCollection $result */
        if ($result === false || (is_array($result) && count($result) < 1)) {
            throw new NotFoundHttpException('No reports found for that query');
        }

        return $result;
    }

    /**
     * @param array $data
     * @return mixed
     */
    private function trimLeftDataHourly(array $data)
    {
        foreach ($data as $key => $value) {
            $hasData = $this->checkHasData($value);

            if ($hasData == true) {

                if ($key > 0) {
                    array_splice($data, 0, $key);

                    return $data;
                } else {
                    return $data;
                }
            }
        }

        return [];
    }

    /**
     * @param $report
     * @return bool
     */
    private function checkHasData($report)
    {
        if (!$report instanceof ReportInterface) {
            return false;
        }

        if ($report->getRequests()
            || $report->getImpressions()
            || $report->getBids()
            || $report->getErrors()
            || $report->getBlocks()
            || $report->getRequestFillRate()

        ) {
            return true;
        }

        return false;
    }

    /**
     * @param array $data
     * @return int
     */
    private function getMinHour(array $data)
    {
        $minHour = 0;
        foreach ($data as $key => $report) {
            if (!$report instanceof ReportInterface) {
                continue;
            }

            $minHour = $report->getDate()->format('G');
            break;
        }

        return (int) $minHour;
    }

    /**
     * @param array $data
     * @param $minHour
     * @param $maxHour
     * @return mixed
     */
    private function filterDataHourly(array $data, $minHour, $maxHour)
    {
        if(empty($data)) {
            return $data;
        }

        // do min hour
        foreach ($data as $key => $report) {

            if (!$report instanceof ReportInterface) {
                unset($data[$key]);
                continue;
            }

            $hour = (int) $report->getDate()->format('G');

            if ($hour < $minHour ) {
                unset($data[$key]);
            }
        }

        // do max hour
        foreach ($data as $key => $report) {

            if (!$report instanceof ReportInterface) {
                unset($data[$key]);
                continue;
            }

            $hour = (int) $report->getDate()->format('G');

            if ($hour > $maxHour ) {
                unset($data[$key]);
            }
        }

        return array_values($data);
    }

    /**
     * @param array $data
     * @param $minHour
     * @param $maxHour
     * @param DateTime $date
     * @return mixed
     */
    private function patchMissingDataHourly(array $data, $minHour, $maxHour, DateTime $date)
    {
        if(empty($data)) {
            return $data;
        }

        // add min hour if the first element of array has hour less than $minHour
        /** @var ReportInterface $firstReport */
        $firstReport = $data[0];
        $hour = (int) $firstReport->getDate()->format('G');
        if ($hour > $minHour) {
            $newDate = $date->setTime($minHour, 0)->format('Y-m-d G');
            $newReportMinHour = clone $firstReport;
            $newReportMinHour->setDate(DateTime::createFromFormat('Y-m-d G', $newDate));

            array_unshift($data, $newReportMinHour);
        }

        // add max hour if the last element of array has hour mor than $maxHour
        /** @var ReportInterface $lastReport */
        $lastReport = $data[count($data) - 1];
        $hour = (int) $lastReport->getDate()->format('G');
        if ($hour < $maxHour) {
            $newDate = $date->setTime($maxHour, 0)->format('Y-m-d G');
            $newReportMaxHour = clone $lastReport;
            $newReportMaxHour->setDate(DateTime::createFromFormat('Y-m-d G', $newDate));

            array_push($data, $newReportMaxHour);
        }

        $newData = [];
        foreach ($data as $key => $report) {

            if (!$report instanceof ReportInterface) {
                unset($data[$key]);
                continue;
            }

            $hour = (int) $report->getDate()->format('G');

            if (!isset($previousHour)) {
                // the first loop
                $newData [] = $report;

                $previousHour = $hour;
                continue;
            }

            if ($hour - 1 == $previousHour ) {
                $newData [] = $report;

                $previousHour = $hour;
                continue;
            }

            // else need to patch missing data
            if ($hour - $previousHour >= 3) {
                for ($i = $previousHour + 1; $i < $hour; $i++) {
                    $newReport = clone $report;
                    $newDate = $date->setTime($i, 0)->format('Y-m-d G');

                    $newReport->setDate(DateTime::createFromFormat('Y-m-d G', $newDate));
                    $newData [] = $newReport;
                    $newReport = [];
                }

                $newData [] = $report;
                $previousHour = $hour;

                continue;
            }

            $newReport = clone $report;
            $newDate = $date->setTime($previousHour + 1, 0)->format('Y-m-d G');

            $newReport->setDate(DateTime::createFromFormat('Y-m-d G', $newDate));
            $newData [] = $newReport;
            $newData [] = $report;
            $newReport = [];

            $previousHour = $previousHour + 1;

        }

        unset($data, $newReport, $hour, $previousHour, $date, $newDate);

        return array_values($newData);
    }
}