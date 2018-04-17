<?php


namespace Tagcade\Bundle\ReportApiBundle\Controller;


use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Service\Report\VideoReport\Parameter\FilterParameter;
use Tagcade\Service\Report\VideoReport\Parameter\MetricParameter;
use Tagcade\Service\Report\VideoReport\Parameter\Parameter;
use Tagcade\Service\Report\VideoReport\Selector\Result\ReportCollection;

class VideoReportController extends FOSRestController
{
    const COMPARISON_TYPE_DAY_OVER_DAY = 'day-over-day';
    const COMPARISON_TYPE_WEEK_OVER_WEEK = 'week-over-week';
    const COMPARISON_TYPE_MONTH_OVER_MONTH = 'month-over-month';
    const COMPARISON_TYPE_YEAR_OVER_YEAR = 'year-over-year';

    static $SUPPORTED_COMPARISON_TYPES = [
        self::COMPARISON_TYPE_DAY_OVER_DAY,
        self::COMPARISON_TYPE_WEEK_OVER_WEEK,
        self::COMPARISON_TYPE_MONTH_OVER_MONTH,
        self::COMPARISON_TYPE_YEAR_OVER_YEAR
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
     */
    public function getVideoReportComparisonAction()
    {
        $comparisonType = $this->get('fos_rest.request.param_fetcher')->get('type');
        if (empty($comparisonType) || !in_array($comparisonType, self::$SUPPORTED_COMPARISON_TYPES)) {
            throw new BadRequestHttpException(sprintf('Not support comparison type %s', $comparisonType));
        }

        /* build common params */
        $filters = [
            FilterParameter::PUBLISHER_KEY => [],
            FilterParameter::VIDEO_WATERFALL_TAG_KEY => [],
            FilterParameter::VIDEO_DEMAND_PARTNER_KEY => [],
            FilterParameter::VIDEO_DEMAND_AD_TAG_KEY => [],
            FilterParameter::VIDEO_PUBLISHER_KEY => [],
            FilterParameter::START_DATE_KEY => '',
            FilterParameter::END_DATE_KEY => ''
        ];

        // NOTICE: need breakdowns day to return multiple "reports" by day inside main report
        $breakdowns = ['day'];

        $metrics = [
            MetricParameter::REQUESTS_KEY,
            MetricParameter::BID_KEY,
            MetricParameter::ERROR_KEY,
            MetricParameter::IMPRESSION_KEY,
            MetricParameter::BLOCKED_REQUEST_KEY,
            MetricParameter::REQUEST_FILL_RATE_KEY
        ];

        /* get startDate-endDate due to comparison type */
        $startDateEndDate = $this->getStartDateEndDateDueToComparisonType($comparisonType);
        if (!is_array($startDateEndDate)) {
            throw new BadRequestHttpException(sprintf('Not support comparison type %s', $comparisonType));
        }

        /* build params for current */
        $filters[FilterParameter::START_DATE_KEY] = $startDateEndDate['current']['startDate'];
        $filters[FilterParameter::END_DATE_KEY] = $startDateEndDate['current']['endDate'];
        $paramsForToday = [
            Parameter::FILTER_KEY => json_encode($filters),
            Parameter::BREAK_DOWN_KEY => json_encode($breakdowns),
            Parameter::METRIC_KEY => json_encode($metrics)
        ];

        /* build params for history */
        $filters[FilterParameter::START_DATE_KEY] = $startDateEndDate['history']['startDate'];
        $filters[FilterParameter::END_DATE_KEY] = $startDateEndDate['history']['endDate'];
        $paramsForYesterday = [
            Parameter::FILTER_KEY => json_encode($filters),
            Parameter::BREAK_DOWN_KEY => json_encode($breakdowns),
            Parameter::METRIC_KEY => json_encode($metrics)
        ];

        try {
            $currentReports = $this->getReportByParams($paramsForToday);
        } catch (\Exception $e) {
            $currentReports = [];
        }

        try {
            $historyReports = $this->getReportByParams($paramsForYesterday);
        } catch (\Exception $e) {
            $historyReports = [];
        }

        $result = [
            'current' => $currentReports,
            'history' => $historyReports
        ];

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
     * @param string $comparisonType
     * @return false|array format as
     * [
     *     'current': [
     *         'startDate': '',
     *         'endDate': ''
     *     ],
     *     'history': [
     *         'startDate': '',
     *         'endDate': ''
     *     ]
     * ]
     */
    private function getStartDateEndDateDueToComparisonType($comparisonType)
    {
        switch ($comparisonType) {
            case self::COMPARISON_TYPE_DAY_OVER_DAY:
                return [
                    'current' => [
                        'startDate' => (new \DateTime('yesterday'))->format('Y-m-d'),
                        'endDate' => (new \DateTime('yesterday'))->format('Y-m-d')
                    ],
                    'history' => [
                        'startDate' => (new \DateTime('-2 days'))->format('Y-m-d'),
                        'endDate' => (new \DateTime('-2 days'))->format('Y-m-d')
                    ]
                ];

            case self::COMPARISON_TYPE_WEEK_OVER_WEEK:
                return [
                    'current' => [
                        'startDate' => (new \DateTime('-7 days'))->format('Y-m-d'),
                        'endDate' => (new \DateTime('yesterday'))->format('Y-m-d')
                    ],
                    'history' => [
                        'startDate' => (new \DateTime('-14 days'))->format('Y-m-d'),
                        'endDate' => (new \DateTime('-8 days'))->format('Y-m-d')
                    ]
                ];

            case self::COMPARISON_TYPE_MONTH_OVER_MONTH:
                return [
                    'current' => [
                        'startDate' => (new \DateTime('-30 days'))->format('Y-m-d'),
                        'endDate' => (new \DateTime('yesterday'))->format('Y-m-d')
                    ],
                    'history' => [
                        'startDate' => (new \DateTime('-60 days'))->format('Y-m-d'),
                        'endDate' => (new \DateTime('-31 days'))->format('Y-m-d')
                    ]
                ];

            case self::COMPARISON_TYPE_YEAR_OVER_YEAR:
                return [
                    'current' => [
                        'startDate' => (new \DateTime('first day of January this year'))->format('Y-m-d'),
                        'endDate' => (new \DateTime('yesterday'))->format('Y-m-d')
                    ],
                    'history' => [
                        'startDate' => (new \DateTime('first day of January last year'))->format('Y-m-d'),
                        'endDate' => (new \DateTime('last day of December last year'))->format('Y-m-d')
                    ]
                ];
        }

        return false;
    }
}