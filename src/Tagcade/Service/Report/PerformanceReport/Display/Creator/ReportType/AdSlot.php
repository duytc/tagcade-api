<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Creator\ReportType;

use Tagcade\Model\Core\AdSlotInterface as AdSlotModelInterface;
use Tagcade\Entity\Report\PerformanceReport\Display\AdSlotReport;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\ReportType\Behaviours\HasSubReports;

class AdSlot extends ReportTypeAbstract implements AdSlotInterface
{
    use HasSubReports;

    /**
     * @var AdTagInterface
     */
    protected $subReportCreator;

    public function __construct(AdTagInterface $subReportCreator)
    {
        $this->subReportCreator = $subReportCreator;
    }

    /**
     * @inheritdoc
     */
    public function doCreateReport(AdSlotModelInterface $adSlot)
    {
        $this->syncEventCounterForSubReports();

        $report = new AdSlotReport();

        $report
            ->setAdSlot($adSlot)
            ->setDate($this->getDate())
            ->setName($adSlot->getName())
            ->setSlotOpportunities($this->eventCounter->getSlotOpportunityCount($adSlot->getId()))
        ;

        foreach ($adSlot->getAdTags() as $adTag) {
            $report->addSubReport(
                $this->subReportCreator->createReport($adTag, $this->getDate())
                    ->setSuperReport($report)
            );
        }

        return $report;
    }

    /**
     * @inheritdoc
     */
    public function checkParameter($adSlot)
    {
        return $adSlot instanceof AdSlotModelInterface;
    }
}