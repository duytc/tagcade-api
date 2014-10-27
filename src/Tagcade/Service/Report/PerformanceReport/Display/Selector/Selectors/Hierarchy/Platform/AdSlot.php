<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Selector\Selectors\Hierarchy\Platform;

use DateTime;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Selectors\AbstractSelector;
use Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\Platform\AdSlotReportRepositoryInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Platform\AdSlot as AdSlotReportType;

class AdSlot extends AbstractSelector
{
    /**
     * @var AdSlotReportRepositoryInterface
     */
    protected $repository;

    public function __construct(AdSlotReportRepositoryInterface $repository)
    {

        $this->repository = $repository;
    }

    protected function doGetReports(AdSlotReportType $reportType, DateTime $startDate, DateTime $endDate)
    {
        return $this->repository->getReportFor($reportType->getAdSlot(), $startDate, $endDate);
    }

    public function supportsReportType(ReportTypeInterface $reportType)
    {
        return $reportType instanceof AdSlotReportType;
    }
}