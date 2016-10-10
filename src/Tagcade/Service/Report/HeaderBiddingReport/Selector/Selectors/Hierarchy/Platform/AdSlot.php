<?php

namespace Tagcade\Service\Report\HeaderBiddingReport\Selector\Selectors\Hierarchy\Platform;

use DateTime;
use Tagcade\Service\Report\HeaderBiddingReport\Selector\Selectors\AbstractSelector;
use Tagcade\Repository\Report\HeaderBiddingReport\Hierarchy\Platform\AdSlotReportRepositoryInterface;
use Tagcade\Model\Report\HeaderBiddingReport\ReportType\ReportTypeInterface;
use Tagcade\Model\Report\HeaderBiddingReport\ReportType\Hierarchy\Platform\AdSlot as AdSlotReportType;

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