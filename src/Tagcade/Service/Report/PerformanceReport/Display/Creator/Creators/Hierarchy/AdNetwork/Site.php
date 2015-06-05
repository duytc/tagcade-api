<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\Hierarchy\AdNetwork;

use Tagcade\Entity\Report\PerformanceReport\Display\AdNetwork\SiteReport;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\CreatorAbstract;
use Tagcade\DomainManager\AdTagManagerInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\HasSubReportsTrait;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\AdNetwork\Site as AdNetworkSiteReportType;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\AdNetwork\AdTag as AdTagReportType;

class Site extends CreatorAbstract implements SiteInterface
{
    use HasSubReportsTrait;

    /**
     * @var AdTagManagerInterface
     */
    private $adTagManager;

    public function __construct(AdTagManagerInterface $adTagManager, AdTagInterface $subReportCreator)
    {
        $this->adTagManager = $adTagManager;
        $this->subReportCreator = $subReportCreator;
    }

    /**
     * @inheritdoc
     */
    public function doCreateReport(AdNetworkSiteReportType $reportType)
    {
        $this->syncEventCounterForSubReports();

        $report = new SiteReport();

        $adNetwork = $reportType->getAdNetwork();
        $site = $reportType->getSite();

        $report
            ->setSite($site)
            ->setDate($this->getDate())
        ;

        $adTags = $this->adTagManager->getAdTagsForAdNetworkAndSite($adNetwork, $site);

        foreach ($adTags as $adTag) {
            $report->addSubReport(
                $this->subReportCreator->createReport(new AdTagReportType($adTag))
            );
        }

        return $report;
    }

    /**
     * @inheritdoc
     */
    public function supportsReportType(ReportTypeInterface $reportType)
    {
        return $reportType instanceof AdNetworkSiteReportType;
    }
}