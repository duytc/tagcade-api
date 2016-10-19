<?php


namespace Tagcade\Service\Report\SourceReport;


use DateTime;
use Tagcade\Bundle\UserBundle\DomainManager\PublisherManagerInterface;
use Tagcade\DomainManager\SiteManagerInterface;
use Tagcade\Model\Core\SiteInterface;
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


    public function getSiteReport(SiteInterface $site, DateTime $startDate, DateTime $endDate)
    {
        return $this->reportSelector->getReports($site, $startDate, $endDate);
    }

    public function getPublisherByDayReport(PublisherInterface $publisher, DateTime $startDate, DateTime $endDate)
    {
        return $this->reportSelector->getPublisherByDayReport($publisher, $startDate, $endDate);
    }

    public function getPublisherBySiteReport(PublisherInterface $publisher, DateTime $startDate, DateTime $endDate)
    {
        return $this->reportSelector->getPublisherBySiteReport($publisher, $startDate, $endDate);
    }

    public function getPlatformByDayReport(DateTime $startDate, DateTime $endDate)
    {
        return $this->reportSelector->getPlatformByDayReport($startDate, $endDate);
    }

    public function getPlatformByPublisherReport(DateTime $startDate, DateTime $endDate)
    {
        return $this->reportSelector->getPlatformByPublisherReport($startDate, $endDate);
    }

    public function getPublisherReport(PublisherInterface $publisher, DateTime $startDate, DateTime $endDate)
    {
        $sites = $this->siteManager->getSitesForPublisher($publisher);
        return $this->reportSelector->getMultipleSiteReports($sites, $startDate, $endDate);
    }

    public function getPublisherReports(array $publishers, DateTime $startDate, DateTime $endDate)
    {
        // TODO: Implement getPublisherReports() method.
    }
}