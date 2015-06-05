<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\Hierarchy\AdNetwork;

use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\CreatorAbstract;
use Tagcade\DomainManager\SiteManagerInterface;
use Tagcade\Entity\Report\PerformanceReport\Display\AdNetwork\AdNetworkReport;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\HasSubReportsTrait;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\AdNetwork\AdNetwork as AdNetworkReportType;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\AdNetwork\Site as AdNetworkSiteReportType;

class AdNetwork extends CreatorAbstract implements AdNetworkInterface
{
    use HasSubReportsTrait;

    /**
     * @var SiteManagerInterface
     */
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
        $adTags = $adNetwork->getAdTags();

        $firstOpportunities = 0;
        $verifiedImpressions = 0;
        $unverifiedImpressions = 0;
        $blankImpressions = 0;

        foreach ($adTags as $adTag) {
            /**
             * @var AdTagInterface $adTag
             */
            $firstOpportunities += (int)$this->eventCounter->getFirstOpportunityCount($adTag->getId());
            $verifiedImpressions += (int)$this->eventCounter->getVerifiedImpressionCount($adTag->getId());
            $unverifiedImpressions += (int)$this->eventCounter->getUnverifiedImpressionCount($adTag->getId());
            $blankImpressions += (int)$this->eventCounter->getBlankImpressionCount($adTag->getId());
        }

        $report
            ->setAdNetwork($adNetwork)
            ->setDate($this->getDate())
            ->setFirstOpportunities($firstOpportunities)
            ->setVerifiedImpressions($verifiedImpressions)
            ->setUnverifiedImpressions($unverifiedImpressions)
            ->setBlankImpressions($blankImpressions)
        ;

        $sites = $this->siteManager->getSitesThatHaveAdTagsBelongingToAdNetwork($adNetwork);

        foreach ($sites as $site) {
            $report->addSubReport(
                $this->subReportCreator->createReport(new AdNetworkSiteReportType($site, $adNetwork))
            );
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