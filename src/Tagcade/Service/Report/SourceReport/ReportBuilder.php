<?php


namespace Tagcade\Service\Report\SourceReport;


use DateTime;
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
     * ReportBuilder constructor.
     * @param ReportSelectorInterface $reportSelector
     * @param SiteManagerInterface $siteManager
     */
    public function __construct(ReportSelectorInterface $reportSelector, SiteManagerInterface $siteManager)
    {
        $this->reportSelector = $reportSelector;
        $this->siteManager = $siteManager;
    }


    public function getSiteReport(SiteInterface $site, DateTime $startDate, DateTime $endDate)
    {
        return $this->reportSelector->getReports($site, $startDate, $endDate);
    }

    public function getPublisherReport(PublisherInterface $publisher, DateTime $startDate, DateTime $endDate)
    {
        $sites = $this->siteManager->getSitesForPublisher($publisher);
        return $this->reportSelector->getMultipleReports($sites, $startDate, $endDate);
    }

}