<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\Hierarchy\Platform;


use Tagcade\DomainManager\AdSlotManagerInterface;
use Tagcade\DomainManager\AdTagManagerInterface;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform\CalculatedReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform\SiteReport;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform\SiteReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Platform\Site as SiteReportType;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Billing\BillingCalculatorInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\Hierarchy\BillableSnapshotCreatorAbstract;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\SnapshotCreatorInterface;

class SiteSnapshot extends BillableSnapshotCreatorAbstract implements SiteInterface, SnapshotCreatorInterface
{
    use ConstructCalculatedReportTrait;

    /**
     * @var AdSlotManagerInterface
     */
    private $adSlotManager;
    /**
     * @var AdTagManagerInterface
     */
    private $adTagManager;

    public function __construct(AdSlotManagerInterface $adSlotManager, AdTagManagerInterface $adTagManager, BillingCalculatorInterface $billingCalculator)
    {
        parent::__construct($billingCalculator);

        $this->adSlotManager = $adSlotManager;
        $this->adTagManager = $adTagManager;
    }

        /**
     * @param SiteReportType $reportType
     * @return SiteReportInterface
     */
    public function doCreateReport(SiteReportType $reportType)
    {
        $report = new SiteReport();
        $site = $reportType->getSite();
        $report
            ->setSite($site)
            ->setName($site->getName())
            ->setDate($this->getDate())
        ;

        $reportableAdSlotIds = $this->adSlotManager->getReportableAdSlotIdsForSite($site);
        $adSlotReportCounts = $this->eventCounter->getAdSlotReports($reportableAdSlotIds);
        unset($reportableAdSlotIds);

        $adTagIdsForSite = $this->adTagManager->getAdTagIdsForSite($site);
        $adTagReportCounts =  $this->eventCounter->getAdTagReports($adTagIdsForSite);
        unset($adTagIdsForSite);

        $this->parseRawReportData($report, array_merge($adSlotReportCounts, $adTagReportCounts));

        return $report;
    }

    /**
     * @param ReportTypeInterface $reportType
     * @return mixed
     */
    public function supportsReportType(ReportTypeInterface $reportType)
    {
        return $reportType instanceof SiteReportType;
    }
}