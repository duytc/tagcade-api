<?php

namespace Tagcade\Service\Report\RtbReport\Selector\Selectors\Hierarchy;

use DateTime;
use Tagcade\Model\Report\RtbReport\ReportType\ReportTypeInterface;
use Tagcade\Repository\Report\RtbReport\Hierarchy\AdSlotReportRepositoryInterface;
use Tagcade\Service\Report\RtbReport\Selector\Selectors\AbstractSelector;
use Tagcade\Model\Report\RtbReport\ReportType\Hierarchy\AdSlot as AdSlotReportType;

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