<?php


namespace Tagcade\Service\Report\SourceReport;


use DateInterval;
use DatePeriod;
use DateTime;
use Tagcade\Bundle\UserBundle\DomainManager\PublisherManagerInterface;
use Tagcade\DomainManager\SiteManagerInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\Report\SourceReport\ReportInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Service\Report\SourceReport\Selector\ReportSelectorInterface;

class ReportBuilder implements ReportBuilderInterface
{
    /**
     * @var ReportSelectorInterface
     */
    protected $reportSelector;

    /**
     * @var SiteManagerInterface
     */
    protected $siteManager;

    /**
     * @var PublisherManagerInterface
     */
    protected $publisherManager;

    /**
     * ReportBuilder constructor.
     * @param ReportSelectorInterface $reportSelector
     * @param SiteManagerInterface $siteManager
     * @param PublisherManagerInterface $publisherManager
     */
    public function __construct(ReportSelectorInterface $reportSelector, SiteManagerInterface $siteManager, PublisherManagerInterface $publisherManager)
    {
        $this->reportSelector = $reportSelector;
        $this->siteManager = $siteManager;
        $this->publisherManager = $publisherManager;
    }

    /**
     * @param SiteInterface $site
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return array
     */
    public function getSiteReport(SiteInterface $site, DateTime $startDate, DateTime $endDate)
    {
        return $this->reportSelector->getReports($site, $startDate, $endDate);
    }

    /**
     * @param PublisherInterface $publisher
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return mixed
     */
    public function getPublisherByDayReport(PublisherInterface $publisher, DateTime $startDate, DateTime $endDate)
    {
        $end = $endDate->modify('+1 day');
        $interval = new DateInterval('P1D');
        $dateRange = new DatePeriod($startDate, $interval ,$end);
        $sites = $this->siteManager->getSitesForPublisher($publisher);
        $reports = [];
        foreach($dateRange as $date) {
            $report = $this->reportSelector->getMultipleSiteReports($sites, $date, $date);
            if ($report instanceof ReportInterface) {
                $reports[] = $report;
            }
        }

        if (empty($reports)) {
            return false;
        }

        $reportCollection = new ReportCollection($startDate, $endDate, $reports);

        return $this->reportGrouper->groupReports($reportCollection);
        return $this->reportSelector->getPublisherByDayReport($publisher, $startDate, $endDate);
    }

    /**
     * @param PublisherInterface $publisher
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return mixed
     */
    public function getPublisherBySiteReport(PublisherInterface $publisher, DateTime $startDate, DateTime $endDate)
    {
        $sites = $this->siteManager->getSitesForPublisher($publisher);
        return $this->reportSelector->getMultipleSiteReports($sites, $startDate, $endDate);
    }

    /**
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return mixed
     */
    public function getPlatformByDayReport(DateTime $startDate, DateTime $endDate)
    {
        return $this->reportSelector->getPlatformByDayReport($startDate, $endDate);
    }

    /**
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return mixed
     */
    public function getPlatformByPublisherReport(DateTime $startDate, DateTime $endDate)
    {
        $publishers = $this->publisherManager->allPublisherWithSourceReportModule();
        return $this->reportSelector->getMultiplePublisherReport($publishers, $startDate, $endDate);
    }
}