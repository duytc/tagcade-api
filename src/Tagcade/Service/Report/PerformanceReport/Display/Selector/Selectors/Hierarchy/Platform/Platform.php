<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Selector\Selectors\Hierarchy\Platform;

use DateTime;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Selectors\AbstractSelector;
use Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\Platform\PlatformReportRepositoryInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Platform\Platform as PlatformReportType;

class Platform extends AbstractSelector
{
    /**
     * @var PlatformReportRepositoryInterface
     */
    protected $repository;

    public function __construct(PlatformReportRepositoryInterface $repository)
    {

        $this->repository = $repository;
    }

    protected function doGetReports(PlatformReportType $reportType, DateTime $startDate, DateTime $endDate)
    {
        // reportType not needed for the query
        return $this->repository->getReportFor($startDate, $endDate);
    }

    public function supportsReportType(ReportTypeInterface $reportType)
    {
        return $reportType instanceof PlatformReportType;
    }
}