<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\Hierarchy\AdNetwork;

use Tagcade\DomainManager\SiteManagerInterface;
use Tagcade\Entity\Report\PerformanceReport\Display\AdNetwork\AdNetworkReport;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\AdNetwork\SiteReport;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\AdNetwork\AdNetwork as AdNetworkReportType;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\AdNetwork\Site as AdNetworkSiteReportType;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\CreatorAbstract;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\HasSubReportsTrait;

class AdNetwork extends CreatorAbstract implements AdNetworkInterface
{
    use HasSubReportsTrait;

    /** @var SiteManagerInterface */
    private $siteManager;

    public function __construct(SiteManagerInterface $siteManager, SiteInterface $subReportCreator)
    {
        $this->siteManager = $siteManager;
        $this->subReportCreator = $subReportCreator;
    }

    /**
     * @inheritdoc
     */
    public function doCreateReport(AdNetworkReportType $reportType)
    {
        $this->syncEventCounterForSubReports();

        $report = new AdNetworkReport();

        $adNetwork = $reportType->getAdNetwork();

        $report
            ->setAdNetwork($adNetwork)
            ->setDate($this->getDate())
            ->setName($adNetwork->getName());

        $sites = $this->siteManager->getSitesThatHaveAdTagsBelongingToAdNetwork($adNetwork);
        foreach ($sites as $site) {
            /**
             * @var SiteReport $currentSiteReport
             */
            $currentSiteReport = $this->subReportCreator->createReport(new AdNetworkSiteReportType($site, $adNetwork));
            $report->addSubReport($currentSiteReport);
        }

        return $report;
    }

    /**
     * @inheritdoc
     */
    public function supportsReportType(ReportTypeInterface $reportType)
    {
        return $reportType instanceof AdNetworkReportType;
    }
}