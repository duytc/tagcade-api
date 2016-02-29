<?php

namespace Tagcade\Service\Report\RtbReport\Selector\Selectors\Hierarchy;

use DateTime;
use Tagcade\Model\Report\RtbReport\ReportType\ReportTypeInterface;
use Tagcade\Repository\Report\RtbReport\Hierarchy\PlatformReportRepositoryInterface;
use Tagcade\Service\Report\RtbReport\Selector\Selectors\AbstractSelector;
use Tagcade\Model\Report\RtbReport\ReportType\Hierarchy\Platform as PlatformReportType;

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