<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Selector\Selectors\Hierarchy\Partner;

use DateTime;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Partner\Account as PartnerAccountReportType;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\Partner\AccountReportRepositoryInterface as PartnerAccountReportRepositoryInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Selectors\AbstractSelector;

class Account extends AbstractSelector
{
    /** @var PartnerAccountReportRepositoryInterface */
    protected $repository;

    public function __construct(PartnerAccountReportRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    protected function doGetReports(PartnerAccountReportType $reportType, DateTime $startDate, DateTime $endDate)
    {
        return $this->repository->getReportFor($reportType->getPublisher(), $startDate, $endDate);
    }

    public function supportsReportType(ReportTypeInterface $reportType)
    {
        return $reportType instanceof PartnerAccountReportType;
    }
}