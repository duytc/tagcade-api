<?php

namespace Tagcade\Service\Report\RtbReport\Selector\Selectors\Hierarchy;

use DateTime;
use Tagcade\Model\Report\RtbReport\ReportType\ReportTypeInterface;
use Tagcade\Repository\Report\RtbReport\Hierarchy\AccountReportRepositoryInterface;
use Tagcade\Service\Report\RtbReport\Selector\Selectors\AbstractSelector;
use Tagcade\Model\Report\RtbReport\ReportType\Hierarchy\Account as AccountReportType;

class Account extends AbstractSelector
{
    /**
     * @var AccountReportRepositoryInterface
     */
    protected $repository;

    public function __construct(AccountReportRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    protected function doGetReports(AccountReportType $reportType, DateTime $startDate, DateTime $endDate)
    {
        return $this->repository->getReportFor($reportType->getPublisher(), $startDate, $endDate);
    }

    public function supportsReportType(ReportTypeInterface $reportType)
    {
        return $reportType instanceof AccountReportType;
    }
}