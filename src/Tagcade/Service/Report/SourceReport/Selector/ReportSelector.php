<?php

namespace Tagcade\Service\Report\SourceReport\Selector;

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
     */
    public function __construct(ReportRepositoryInterface $repository, DateUtil $dateUtil, SiteRepositoryInterface $siteRepository, ReportGrouperInterface $reportGrouper)
    {
        $this->repository = $repository;
        $this->dateUtil = $dateUtil;
        $this->siteRepository = $siteRepository;
        $this->reportGrouper = $reportGrouper;
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

    public function getMultipleReports(array $sites, DateTime $startDate, DateTime $endDate)
    {
        $reports = [];

        foreach($sites as $site) {
            if (!$site instanceof SiteInterface) {
                throw new InvalidArgumentException('expect "SiteInterface" object');
            }

            $reports[] = $this->getReports($site, $startDate, $endDate);
        }

        if (empty($reports)) {
            return false;
        }

        $reportCollection = new ReportCollection($startDate, $endDate, $reports);

        return $this->reportGrouper->groupReports($reportCollection);
    }

    public function getMultipleSiteReports(array $sites, DateTime $startDate, DateTime $endDate)
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

            $reports[] = $this->getReports($site, $startDate, $endDate);
        }

        if (empty($reports)) {
            return false;
        }

        $reportCollection = new ReportCollection($startDate, $endDate, $reports, $reportName);

        return $this->reportGrouper->groupReports($reportCollection);
    }

    public function getMultiplePublisherReport(array $publishers, DateTime $startDate, DateTime $endDate)
    {
        $reports = [];

        foreach($publishers as $publisher) {
            if (!$publisher instanceof PublisherInterface) {
                throw new InvalidArgumentException('expect "PublisherInterface" object');
            }

            $sites = $this->siteRepository->getSitesForPublisher($publisher);
            $reports[] = $this->getMultipleSiteReports($sites, $startDate, $endDate);
        }

        if (empty($reports)) {
            return false;
        }

        $reportCollection = new ReportCollection($startDate, $endDate, $reports);

        return $this->reportGrouper->groupReports($reportCollection);
    }
}