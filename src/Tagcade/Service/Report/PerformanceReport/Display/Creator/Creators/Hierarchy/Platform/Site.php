<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\Hierarchy\Platform;

use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\CreatorAbstract;
use Tagcade\Entity\Report\PerformanceReport\Display\Platform\SiteReport;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\HasSubReportsTrait;

use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Platform\Site as SiteReportType;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Platform\AdSlot as AdSlotReportType;

class Site extends CreatorAbstract implements SiteInterface
{
    use HasSubReportsTrait;

    public function __construct(AdSlotInterface $subReportCreator)
    {
        $this->subReportCreator = $subReportCreator;
    }

    /**
     * @inheritdoc
     */
    public function doCreateReport(SiteReportType $reportType)
    {
        $this->syncEventCounterForSubReports();

        $report = new SiteReport();

        $site = $reportType->getSite();

        $report
            ->setSite($site)
            ->setDate($this->getDate())
        ;

        $allAdSlots = $site->getReportableAdSlots();

        foreach ($allAdSlots as $adSlot) {
            $report->addSubReport(
                $this->subReportCreator->createReport(new AdSlotReportType($adSlot))
                    ->setSuperReport($report)
            );
        }

        return $report;
    }

    /**
     * @inheritdoc
     */
    public function supportsReportType(ReportTypeInterface $reportType)
    {
        return $reportType instanceof SiteReportType;
    }
}