<?php

namespace Tagcade\Service\Report\SourceReport\Selector;

use DateInterval;
use DatePeriod;
use DateTime;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Exception\LogicException;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\Report\SourceReport\ReportInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Core\SiteRepositoryInterface;
use Tagcade\Repository\Report\SourceReport\ReportRepositoryInterface;
use Tagcade\Service\DateUtil;
use Tagcade\Exception\Report\InvalidDateException;
use Tagcade\Service\Report\SourceReport\Result\ReportCollection;
use Tagcade\Service\Report\SourceReport\Selector\Grouper\ReportGrouperInterface;

class ReportSelector implements ReportSelectorInterface
{
    /**
     * @var ReportRepositoryInterface
     */
    protected $repository;

    /**
     * @var SiteRepositoryInterface
     */
    protected $siteRepository;

    /**
     * @var PublisherManagerInterface
     */
    protected $publisherManager;

    /**
     * @var DateUtil
     */
    protected $dateUtil;

    /**
     * @var ReportGrouperInterface
     */
    protected $reportGrouper;

    /**
     * @param ReportRepositoryInterface $repository
     * @param DateUtil $dateUtil
     * @param SiteRepositoryInterface $siteRepository
     * @param ReportGrouperInterface $reportGrouper
     * @param PublisherManagerInterface $publisherManager
     */
    public function __construct(ReportRepositoryInterface $repository, DateUtil $dateUtil, SiteRepositoryInterface $siteRepository,
                                ReportGrouperInterface $reportGrouper, PublisherManagerInterface $publisherManager)
    {
        $this->repository = $repository;
        $this->dateUtil = $dateUtil;
        $this->siteRepository = $siteRepository;
        $this->reportGrouper = $reportGrouper;
        $this->publisherManager = $publisherManager;
    }

    /**
     * @inheritdoc
     */
    public function getReports(SiteInterface $site, DateTime $startDate, DateTime $endDate = null, $rowOffset = 0, $rowLimit = null)
    {
        if (!$endDate) {
            $endDate = $startDate;
        }

        if ($startDate > $endDate) {
            throw new InvalidDateException('start date must be before the end date');
        }

        $reports = $this->repository->getReports($site, $startDate, $endDate);

        if (!$reports) {
            return false;
        }

        $reportName = null;
        foreach($reports as $report) {
            if (!$report instanceof ReportInterface) {
                throw new LogicException('You tried to add reports to a collection that did not match the supplied report type');
            }

            if (null === $reportName) {
                $reportName = $report->getSite()->getName();
            }

            unset($report);
        }

        $dates = array_map(function(ReportInterface $report) {
            return $report->getDate();
        }, $reports);

        // instead of using user-supplied dates for the collection date range
        // determine what the actual date range is

        $actualStartDate = min($dates);
        $actualEndDate = max($dates);

        $reportCollection = new ReportCollection($actualStartDate, $actualEndDate, $reports, $reportName);

        unset($dates, $actualStartDate, $actualEndDate);

        return $this->reportGrouper->groupReports($reportCollection);
    }

    public function getPublisherByDayReport(PublisherInterface $publisher, DateTime $startDate, DateTime $endDate)
    {
        $end = $endDate->modify('+1 day');
        $interval = new DateInterval('P1D');
        $dateRange = new DatePeriod($startDate, $interval ,$end);
        $sites = $this->siteRepository->getSitesForPublisher($publisher);
        $reports = [];
        foreach($dateRange as $date) {
            $report = $this->getMultipleSiteReports($sites, $date, $date);
            if ($report instanceof ReportInterface) {
                $reports[] = $report;
            }
        }

        if (empty($reports)) {
            return false;
        }

        $reportCollection = new ReportCollection($startDate, $endDate, $reports);

        return $this->reportGrouper->groupReports($reportCollection);
    }

    public function getPublisherBySiteReport(PublisherInterface $publisher, DateTime $startDate, DateTime $endDate)
    {
        $sites = $this->siteRepository->getSitesForPublisher($publisher);
        return $this->getMultipleSiteReports($sites, $startDate, $endDate);
    }

    public function getPlatformByDayReport(DateTime $startDate, DateTime $endDate)
    {
        $end = $endDate->modify('+1 day');
        $interval = new DateInterval('P1D');
        $dateRange = new DatePeriod($startDate, $interval ,$end);
        $publishers = $this->publisherManager->allPublisherWithSourceReportModule();
        $reports = [];

        foreach($dateRange as $date) {
            $report = $this->getMultiplePublisherReport($publishers, $date, $date);
            if ($report instanceof ReportInterface) {
                $reports[] = $report;
            }
        }

        if (empty($reports)) {
            return false;
        }

        $reportCollection = new ReportCollection($startDate, $endDate, $reports);

        return $this->reportGrouper->groupReports($reportCollection);
    }

    public function getPlatformByPublisherReport(DateTime $startDate, DateTime $endDate)
    {
        $publishers = $this->publisherManager->allPublisherWithSourceReportModule();
        return $this->getMultiplePublisherReport($publishers, $startDate, $endDate);
    }
    /**
     * @param array $sites
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return bool|ReportInterface
     */
    protected function getMultipleSiteReports(array $sites, DateTime $startDate, DateTime $endDate)
    {
        $reports = [];
        $reportName = null;
        foreach($sites as $site) {
            if (!$site instanceof SiteInterface) {
                throw new InvalidArgumentException('expect "SiteInterface" object');
            }

            if (null === $reportName) {
                $reportName = $site->getPublisher()->getUser()->getUsername();
            }

            $report = $this->getReports($site, $startDate, $endDate);
            if ($report instanceof ReportInterface) {
                $reports[] = $report;
            }
        }

        if (empty($reports)) {
            return false;
        }

        $reportCollection = new ReportCollection($startDate, $endDate, $reports, $reportName);

        return $this->reportGrouper->groupReports($reportCollection);
    }

    /**
     * @param array $publishers
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return bool|ReportInterface
     */
    protected function getMultiplePublisherReport(array $publishers, DateTime $startDate, DateTime $endDate)
    {
        $reports = [];

        foreach($publishers as $publisher) {
            if (!$publisher instanceof PublisherInterface) {
                throw new InvalidArgumentException('expect "PublisherInterface" object');
            }

            $sites = $this->siteRepository->getSitesForPublisher($publisher);
            $report = $this->getMultipleSiteReports($sites, $startDate, $endDate);
            if ($report instanceof ReportInterface) {
                $reports[] = $report;
            }
        }

        if (empty($reports)) {
            return false;
        }

        $reportCollection = new ReportCollection($startDate, $endDate, $reports);

        return $this->reportGrouper->groupReports($reportCollection);
    }
}