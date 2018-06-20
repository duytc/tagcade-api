<?php

namespace Tagcade\Bundle\StatisticsApiBundle\Controller;

use DateInterval;
use DatePeriod;
use DateTime;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Platform as PlatformReportTypes;

class StatisticsController extends FOSRestController
{
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
     * @Security("has_role('ROLE_ADMIN')")
     *
     * Get statistics for the platform with optional date range.
     *
     * @Rest\QueryParam(name="startDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="endDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     *
     * @return array
     */
    public function getPlatformAction()
    {
        $paramFetcher = $this->get('fos_rest.request.param_fetcher');
        $dateUtil = $this->get('tagcade.service.date_util');

        $startDate = $dateUtil->getDateTime($paramFetcher->get('startDate'));
        $endDate = $dateUtil->getDateTime($paramFetcher->get('endDate'));

        return $this->get('tagcade.service.statistics')
            ->getAdminDashboard($startDate, $endDate);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     *
     * Get projected bill for the platform with optional date range.
     *
     * @return array
     */
    public function getPlatformProjectedbillAction()
    {
        return $this->get('tagcade.service.statistics')
            ->getProjectedBilledAmountForAllPublishers();
    }

    /**
     *
     * Get statistics for a publisher with optional date range.
     *
     * @Rest\QueryParam(name="startDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="endDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     *
     * @param int $publisherId
     *
     * @return array
     */
    public function getAccountAction($publisherId)
    {
        $publisher = $this->getPublisher($publisherId);

        $paramFetcher = $this->get('fos_rest.request.param_fetcher');
        $dateUtil = $this->get('tagcade.service.date_util');

        $startDate = $dateUtil->getDateTime($paramFetcher->get('startDate'));
        $endDate = $dateUtil->getDateTime($paramFetcher->get('endDate'));

        return $this->get('tagcade.service.statistics')
            ->getPublisherDashboard($publisher, $startDate, $endDate);
    }

    /**
     * @Rest\Get("/accounts/{publisherId}/projectedbill", requirements={"publisherId" = "\d+"})
     *
     * @param $publisherId
     * @return string
     */
    public function getAccountProjectedBillAction($publisherId)
    {
        $publisher = $this->getPublisher($publisherId);

        return $this->get('tagcade.service.statistics')->getProjectedBilledAmountForPublisher($publisher);
    }

    /**
     *
     * Get summary stats for a publisher with month range (month format YYYY-MM).
     *
     * @Rest\Get("/accounts/{publisherId}/summary", requirements={"publisherId" = "\d+"})
     *
     * @Rest\QueryParam(name="startMonth", requirements="\d{4}-\d{2}", nullable=false)
     * @Rest\QueryParam(name="endMonth", requirements="\d{4}-\d{2}")
     *
     * @param int $publisherId
     *
     * @return array
     */
    public function getAccountSummaryAction($publisherId)
    {
        $publisher = $this->getPublisher($publisherId);
        $paramFetcher = $this->get('fos_rest.request.param_fetcher');

        $startMonth = DateTime::createFromFormat('Y-m', $paramFetcher->get('startMonth'));
        $endMonth = DateTime::createFromFormat('Y-m', $paramFetcher->get('endMonth'));

        return $this->get('tagcade.service.statistics')->getAccountSummaryByMonth($publisher, $startMonth, $endMonth);
    }

    /**
     *
     * @Security("has_role('ROLE_ADMIN')")
     *
     * Get summary stats for a platform with month range (month format YYYY-MM).
     *
     * @Rest\Get("/platform/summary")
     *
     * @Rest\QueryParam(name="startMonth", requirements="\d{4}-\d{2}", nullable=false)
     * @Rest\QueryParam(name="endMonth", requirements="\d{4}-\d{2}")
     *
     *
     * @return array
     */
    public function getPlatformSummaryAction()
    {
        $paramFetcher = $this->get('fos_rest.request.param_fetcher');

        $startMonth = DateTime::createFromFormat('Y-m', $paramFetcher->get('startMonth'));
        $endMonth = DateTime::createFromFormat('Y-m', $paramFetcher->get('endMonth'));

        return $this->get('tagcade.service.statistics')->getPlatformSummaryByMonth($startMonth, $endMonth);
    }

    /**
     * @Rest\Get("/sites/{siteId}/projectedbill", requirements={"siteId" = "\d+"})
     *
     * @param $siteId
     * @return string
     */
    public function getSiteProjectedBillAction($siteId)
    {
        $site = $this->getSite($siteId);

        return $this->get('tagcade.service.statistics')->getProjectedBilledAmountForSite($site);
    }

    /**
     * @param int $publisherId
     * @return \Tagcade\Model\User\Role\PublisherInterface
     */
    protected function getPublisher($publisherId)
    {
        $publisher = $this->get('tagcade_user.domain_manager.publisher')->findPublisher($publisherId);

        if (!$publisher) {
            throw new NotFoundHttpException('That publisher does not exist');
        }

        if (false === $this->get('security.context')->isGranted('view', $publisher)) {
            throw new AccessDeniedException('You do not have permission to view this');
        }

        return $publisher;
    }

    /**
     * @param int $siteId
     * @return \Tagcade\Model\Core\SiteInterface
     */
    protected function getSite($siteId)
    {
        $site = $this->get('tagcade.domain_manager.site')->find($siteId);

        if (!$site) {
            throw new NotFoundHttpException('That site does not exist');
        }

        // check voters
        if (false === $this->get('security.context')->isGranted('view', $site)) {
            throw new AccessDeniedException(sprintf('You do not have permission to view this'));
        }

        return $site;
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @Rest\Get("platform/comparison")
     * Get statistics for the platform with comparison type
     *
     * @Rest\QueryParam(name="type", nullable=false)
     * @Rest\QueryParam(name="currentStartDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="currentEndDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="historyStartDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="historyEndDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     *
     * @ApiDoc(
     *  section = "Statistics report",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  },
     *  parameters = {
     *      {"name"="type", "dataType"="string", "required"=true, "description"="comparison type for Statistics report, such as day-over-day, week-over-week, month-over-month, year-over-year"},
     *  }
     * )
     *
     * @return array
     */
    public function getPlatformComparisonAction()
    {
        $paramFetcher = $this->get('fos_rest.request.param_fetcher');
        $dateUtil = $this->get('tagcade.service.date_util');
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
            /* get start-end Date based on comparison type */
            $startDateEndDate = $this->getStartDateEndDateDueToComparisonType($comparisonType);
        }

        // get start-end history Date
        $startDateHistory = $dateUtil->getDateTime($startDateEndDate['history']['startDate']);
        $endDateHistory = $dateUtil->getDateTime($startDateEndDate['history']['endDate']);

        // get start-end current
        $startDateCurrent = $dateUtil->getDateTime($startDateEndDate['current']['startDate']);
        $endDateCurrent = $dateUtil->getDateTime($startDateEndDate['current']['endDate']);

        // get current report
        if (isset($startDateCurrent) && isset($endDateCurrent)) {
            $currentData = $this->get('tagcade.service.statistics')->getAdminDashboard($startDateCurrent, $endDateCurrent);
        }

        // get history report
        if (isset($startDateHistory) && isset($endDateHistory)) {
            $historyData = $this->get('tagcade.service.statistics')->getAdminDashboard($startDateHistory, $endDateHistory);
        }

        $result = [
            'current' => isset($currentData) ? $currentData : [],
            'history' => isset($historyData) ? $historyData : [],
            'startEndDateCurrent' => isset($startDateEndDate['current']) ? $startDateEndDate['current'] : []
        ];

        if ($comparisonType == self::COMPARISON_TYPE_DAY_OVER_DAY) {
            /* current */
            $maxHour = (new DateTime())->format('G');

            /* get hourly */
            $reportHourToday = $this->get('tagcade.service.statistics')->getAdminDashboardHourly($startDateCurrent);
            $reportHourHistory = $this->get('tagcade.service.statistics')->getAdminDashboardHourly($startDateHistory);

            // trim left today
            $reportHourToday = $this->trimLeftDataHourly($reportHourToday);

            /* calculate min hour */
            $minHour = $this->getMinHour($reportHourToday);

            /*
             * => expected hour range = [minHour, maxHour]
             */

            /* filter yesterday */
            $reportHourToday = $this->filterDataHourly($reportHourToday, $minHour, $maxHour);
            $reportHourHistory = $this->filterDataHourly($reportHourHistory, $minHour, $maxHour);

            /* patch missing for today, yesterday */
            $reportHourToday = $this->patchMissingDataHourly($reportHourToday, $minHour, $maxHour, new DateTime('now'));
            $reportHourHistory = $this->patchMissingDataHourly($reportHourHistory, $minHour, $maxHour,  new DateTime('yesterday'));

            /* result */
            $resultReportHourly = [
                'reportHourToday' => $reportHourToday,
                'reportHourHistory' => $reportHourHistory
            ];

            $result = array_merge($resultReportHourly, $result);
        }

        return $result;
    }

    /**
     * @Rest\Get("accounts/comparison/{publisherId}", requirements={"publisherId" = "\d+"})
     *
     * Get statistics for a publisher with optional date range.
     *
     * @Rest\QueryParam(name="type", nullable=false)
     * @Rest\QueryParam(name="currentStartDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="currentEndDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="historyStartDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="historyEndDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     *
     *
     * @ApiDoc(
     *  section = "Statistics report",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  },
     *  parameters = {
     *      {"name"="type", "dataType"="string", "required"=true, "description"="comparison type for Statistics report, such as day-over-day, week-over-week, month-over-month, year-over-year"},
     *  }
     * )
     *
     * @param int $publisherId
     * @return array
     */
    public function getAccountComparisonAction($publisherId)
    {
        $publisher = $this->getPublisher($publisherId);
        $paramFetcher = $this->get('fos_rest.request.param_fetcher');
        $dateUtil = $this->get('tagcade.service.date_util');

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
            /* get start-end Date based on comparison type */
            $startDateEndDate = $this->getStartDateEndDateDueToComparisonType($comparisonType);
        }

        if (!is_array($startDateEndDate)) {
            throw new BadRequestHttpException(sprintf('Do not support comparison type %s', $comparisonType));
        }

        // get start-end history Date
        $startDateHistory = $dateUtil->getDateTime($startDateEndDate['history']['startDate']);
        $endDateHistory = $dateUtil->getDateTime($startDateEndDate['history']['endDate']);

        // get start-end current
        $startDateCurrent = $dateUtil->getDateTime($startDateEndDate['current']['startDate']);
        $endDateCurrent = $dateUtil->getDateTime($startDateEndDate['current']['endDate']);

        // get current report
        if (isset($startDateCurrent) && isset($endDateCurrent)) {
            if ($comparisonType == self::COMPARISON_TYPE_DAY_OVER_DAY) {
                // try get report for current hour, that means current data
                $currentData = $this->get('tagcade.service.statistics.util.account_report_cache')->getPublisherDashboardSnapshot($publisher, $startDateCurrent);

                // DO NOT need fallback to aggregate from cache, this takes more time. TODO: remove...
                // if (empty($currentData)) {
                //     // force get from redis again by creator
                //     $currentData = $this->get('tagcade.service.statistics')
                //         ->getPublisherDashboard($publisher, $startDateCurrent, $endDateCurrent);
                // }
            } else {
                $currentData = $this->get('tagcade.service.statistics')
                    ->getPublisherDashboard($publisher, $startDateCurrent, $endDateCurrent);
            }
        }

        // get history report
        if (isset($startDateHistory) && isset($endDateHistory)) {
            if ($comparisonType == self::COMPARISON_TYPE_DAY_OVER_DAY) {
                $historyData = $this->get('tagcade.service.statistics.util.account_report_cache')->getPublisherDashboardSnapshot($publisher, $startDateHistory);

                // DO NOT need fallback to aggregate from cache, this takes more time. TODO: remove...
                // if (empty($historyData)) {
                //     // force get from redis again by creator
                //     $historyData = $this->get('tagcade.service.statistics')
                //         ->getPublisherDashboard($publisher, $startDateHistory, $endDateHistory);
                // }
            } else {
                $historyData = $this->get('tagcade.service.statistics')
                    ->getPublisherDashboard($publisher, $startDateHistory, $endDateHistory);
            }
        }

        $result = [
            'current' => isset($currentData) ? $currentData : [],
            'history' => isset($historyData) ? $historyData : [],
            'startEndDateCurrent' => isset($startDateEndDate['current']) ? $startDateEndDate['current'] : []
        ];

        if ($comparisonType == self::COMPARISON_TYPE_DAY_OVER_DAY) {
            /* current */
            $maxHour = (new DateTime())->format('G');

            /* get hourly */
            $reportHourToday = $this->get('tagcade.service.statistics')->getPublisherDashboardHourly($publisher, $startDateCurrent);
            $reportHourHistory = $this->get('tagcade.service.statistics')->getPublisherDashboardHourly($publisher, $startDateHistory);

            // trim left today
            $reportHourToday = $this->trimLeftDataHourly($reportHourToday);

            /* calculate min hour */
            $minHour = $this->getMinHour($reportHourToday);

            /*
             * => expected hour range = [minHour, maxHour]
             */

            /* filter yesterday */
            $reportHourToday = $this->filterDataHourly($reportHourToday, $minHour, $maxHour);
            $reportHourHistory = $this->filterDataHourly($reportHourHistory, $minHour, $maxHour);

            /* patch missing for today, yesterday */
            $reportHourToday = $this->patchMissingDataHourly($reportHourToday, $minHour, $maxHour,  new DateTime('now'));
            $reportHourHistory = $this->patchMissingDataHourly($reportHourHistory, $minHour, $maxHour,  new DateTime('yesterday'));

            /* result */
            $resultReportHourly = [
                'reportHourToday' => $reportHourToday,
                'reportHourHistory' => $reportHourHistory
            ];

            $result = array_merge($resultReportHourly, $result);
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
            case self::COMPARISON_TYPE_YESTERDAY:
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
            case self::COMPARISON_TYPE_DAY_OVER_DAY:
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

                    return array_values($data);
                } else {
                    return array_values($data);
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

        if ($report->getBilledAmount()
            || $report->getEstRevenue()
            || $report->getSlotOpportunities()
            || $report->getTotalOpportunities()
            || $report->getPassbacks()
            || $report->getImpressions()
            || $report->getFillRate()

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

            // get minHour from the first element
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

        // do min hour: delete elements that have hour less than minHour
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

        // do max hour: delete elements that have hour more than minHour
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
                // for from the second loop
                $newData [] = $report;

                $previousHour = $hour;
                continue;
            }

            // else need to patch missing data
            // data [0, 1, 4, 5, 6] ==> expected: [0, 1, 2, 3, 4, 5, 6]
            // or data [0, 1, 5, 6] ==> expected: [0, 1, 2, 3, 4, 5, 6]
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

            // data [0, 1, 3, 4, 5, 6] ==> expected: [0, 1, 2, 3, 4, 5, 6]
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
