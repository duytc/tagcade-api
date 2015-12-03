<?php

namespace Tagcade\Service\Report\UnifiedReport\Selector\PulsePoint;

use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Report\UnifiedReport\ReportType\ReportTypeInterface;
use Tagcade\Repository\Report\UnifiedReport\PulsePoint\AccountManagementRepositoryInterface;
use Tagcade\Service\Report\UnifiedReport\Selector\SelectorInterface;
use Tagcade\Service\Report\UnifiedReport\Selector\UnifiedReportParams;
use Tagcade\Model\Report\UnifiedReport\ReportType\PulsePoint\AccountManagement as AccountManagementReportType;

class AccountManagement implements SelectorInterface
{
    /**
     * @var AccountManagementRepositoryInterface
     */
    protected $accMngRepository;

    function __construct(AccountManagementRepositoryInterface $accMngRepository)
    {
        $this->accMngRepository = $accMngRepository;
    }

    public function getReports(ReportTypeInterface $reportType, UnifiedReportParams $params)
    {
        if (!$reportType instanceof AccountManagementReportType) {
            throw new InvalidArgumentException('Expect instance of AccountManagementReportType');
        }

        return $this->accMngRepository->getReportFor($reportType->getPublisher(), $params->getStartDate(), $params->getEndDate());
    }


    public function supportReport(ReportTypeInterface $reportType)
    {
        return $reportType instanceof AccountManagementReportType;
    }
}