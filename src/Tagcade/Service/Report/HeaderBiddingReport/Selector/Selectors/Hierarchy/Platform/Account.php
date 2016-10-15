<?php

namespace Tagcade\Service\Report\HeaderBiddingReport\Selector\Selectors\Hierarchy\Platform;

use DateTime;
use Tagcade\Service\Report\HeaderBiddingReport\Selector\Selectors\AbstractSelector;
use Tagcade\Repository\Report\HeaderBiddingReport\Hierarchy\Platform\AccountReportRepositoryInterface;
use Tagcade\Model\Report\HeaderBiddingReport\ReportType\ReportTypeInterface;
use Tagcade\Model\Report\HeaderBiddingReport\ReportType\Hierarchy\Platform\Account as AccountReportType;

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