<?php


namespace Tagcade\Behaviors;


use Tagcade\Bundle\ReportApiBundle\Controller\VideoReportController;
use Tagcade\Model\Core\LibraryVideoDemandAdTagInterface;
use Tagcade\Model\Core\VideoDemandAdTagInterface;
use Tagcade\Model\Core\VideoTargetingInterface;
use Tagcade\Model\Core\VideoWaterfallTagInterface;
use Tagcade\Service\Report\VideoReport\Parameter\FilterParameter;
use Tagcade\Service\Report\VideoReport\Parameter\MetricParameter;
use Tagcade\Service\Report\VideoReport\Parameter\Parameter;
use Tagcade\Service\StringUtilTrait;

trait VideoUtilTrait
{
    use StringUtilTrait;
    
    /**
     * @param VideoWaterfallTagInterface $videoWaterfallTag
     * @return VideoWaterfallTagInterface
     */
    public function addDefaultValueForVideoWaterfallTag(VideoWaterfallTagInterface $videoWaterfallTag)
    {
        if (empty($videoWaterfallTag->getUuid())) {
            $uuid = $this->generateUuidV4();
            $videoWaterfallTag->setUuid($uuid);
        }

        if (empty($videoWaterfallTag->getVideoWaterfallTagItems())) {
            $videoWaterfallTag->setVideoWaterfallTagItems([]);
        }

        if (empty($videoWaterfallTag->getTargeting())) {
            $videoWaterfallTag->setTargeting([]);
        }

        if (empty($videoWaterfallTag->getPlatform())) {
            $videoWaterfallTag->setPlatform(['flash']);
        }

        if (empty($videoWaterfallTag->getRunOn())) {
            $videoWaterfallTag->setRunOn('Server-Side VAST+VAPID');
        }

        return $videoWaterfallTag;
    }

    /**
     * @param LibraryVideoDemandAdTagInterface $libraryVideoDemandAdTag
     * @return LibraryVideoDemandAdTagInterface
     */
    public function addDefaultValueForLibraryVideoDemandAdTag(LibraryVideoDemandAdTagInterface $libraryVideoDemandAdTag)
    {
        if (empty($libraryVideoDemandAdTag->getTargeting())) {
            $libraryVideoDemandAdTag->setTargeting([
                VideoTargetingInterface::TARGETING_KEY_COUNTRIES => [],
                VideoTargetingInterface::TARGETING_KEY_DOMAINS => [],
                VideoTargetingInterface::TARGETING_KEY_EXCLUDE_COUNTRIES => [],
                VideoTargetingInterface::TARGETING_KEY_EXCLUDE_DOMAINS => [],
                VideoTargetingInterface::TARGETING_KEY_PLATFORM => [],
                VideoTargetingInterface::TARGETING_KEY_PLAYER_SIZE => [],
                VideoTargetingInterface::TARGETING_KEY_REQUIRED_MACROS => []]);
        }

        return $libraryVideoDemandAdTag;
    }

    /**
     * @param VideoDemandAdTagInterface $videoDemandAdTag
     * @return VideoDemandAdTagInterface
     */
    public function addDefaultValuesForVideoDemandAdTag(VideoDemandAdTagInterface $videoDemandAdTag) {
        $videoDemandAdTag->setTargetingOverride(false);

        return $videoDemandAdTag;
    }

    /**
     * @param VideoDemandAdTagInterface $videoDemandAdTag
     * @return VideoDemandAdTagInterface
     */
    public function addDefaultValueForVideoDemandAdTag(VideoDemandAdTagInterface $videoDemandAdTag) {
        $videoDemandAdTag->setTargetingOverride(false);

        return $videoDemandAdTag;
    }

    /**
     * @param $startDateEndDate
     * @return array
     */
    public function createParamsForReportComparison($startDateEndDate)
    {
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

        return [$paramsForToday, $paramsForYesterday];
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
            case VideoReportController::COMPARISON_TYPE_YESTERDAY:
                return [
                    'current' => [
                        'startDate' => (new \DateTime('yesterday'))->format('Y-m-d'),
                        'endDate' => (new \DateTime('yesterday'))->format('Y-m-d')
                    ],
                    'history' => [
                        'startDate' => (new \DateTime('yesterday'))->format('Y-m-d'),
                        'endDate' => (new \DateTime('yesterday'))->format('Y-m-d')
                    ]
                ];
            case VideoReportController::COMPARISON_TYPE_DAY_OVER_DAY:
                return [
                    'current' => [
                        'startDate' => (new \DateTime('now'))->format('Y-m-d'),
                        'endDate' => (new \DateTime('now'))->format('Y-m-d')
                    ],
                    'history' => [
                        'startDate' => (new \DateTime('yesterday'))->format('Y-m-d'),
                        'endDate' => (new \DateTime('yesterday'))->format('Y-m-d')
                    ]
                ];

            case VideoReportController::COMPARISON_TYPE_WEEK_OVER_WEEK:
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

            case VideoReportController::COMPARISON_TYPE_MONTH_OVER_MONTH:
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

            case VideoReportController::COMPARISON_TYPE_YEAR_OVER_YEAR:
                return [
                    'current' => [
                        'startDate' => (new \DateTime('first day of January this year'))->format('Y-m-d'),
                        'endDate' => (new \DateTime('now'))->format('Y-m-d')
                    ],
                    'history' => [
                        'startDate' => (new \DateTime('first day of January last year'))->format('Y-m-d'),
                        'endDate' => (new \DateTime('-1 year'))->format('Y-m-d')
                    ]
                ];
        }

        return false;
    }
}