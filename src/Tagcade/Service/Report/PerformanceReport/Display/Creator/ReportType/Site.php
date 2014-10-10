<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Creator\ReportType;

use Tagcade\Model\Core\SiteInterface as SiteModelInterface;
use Tagcade\Entity\Report\PerformanceReport\Display\SiteReport;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\ReportType\Behaviours\HasSubReports;

class Site extends ReportTypeAbstract implements SiteInterface
{
    use HasSubReports;

    /**
     * @var AdSlotInterface
     */
    protected $subReportCreator;

    public function __construct(AdSlotInterface $subReportCreator)
    {
        $this->subReportCreator = $subReportCreator;
    }

    /**
     * @inheritdoc
     */
    public function doCreateReport(SiteModelInterface $site)
    {
        $this->syncEventCounterForSubReports();

        $report = new SiteReport();

        $report
            ->setSite($site)
            ->setDate($this->getDate())
            ->setName($site->getName())
        ;

        foreach ($site->getAdSlots() as $adSlot) {
            $report->addSubReport(
                $this->subReportCreator->createReport($adSlot, $this->getDate())
                    ->setSuperReport($report)
            );
        }

        return $report;
    }

    /**
     * @inheritdoc
     */
    public function checkParameter($site)
    {
        return $site instanceof SiteModelInterface;
    }
}