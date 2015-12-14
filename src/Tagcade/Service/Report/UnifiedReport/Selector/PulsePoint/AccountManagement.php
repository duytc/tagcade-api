<?php

namespace Tagcade\Service\Report\UnifiedReport\Selector\PulsePoint;

use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Report\UnifiedReport\ReportType\PulsePoint\AccountManagement as AccountManagementReportType;
use Tagcade\Model\Report\UnifiedReport\ReportType\ReportTypeInterface;
use Tagcade\Repository\Report\UnifiedReport\PulsePoint\AccountManagementRepositoryInterface;
use Tagcade\Service\Report\UnifiedReport\Selector\SelectorInterface;
use Tagcade\Service\Report\UnifiedReport\Selector\UnifiedReportParams;

class AccountManagement implements SelectorInterface
{
    protected $defaultPageRange;

    /**
     * @var AccountManagementRepositoryInterface
     */
    protected $accMngRepository;

    function __construct(AccountManagementRepositoryInterface $accMngRepository, $defaultPageRange)
    {
        $this->accMngRepository = $accMngRepository;
        $this->defaultPageRange = $defaultPageRange;
    }

    public function getReports(ReportTypeInterface $reportType, UnifiedReportParams $params)
    {
        if (!$reportType instanceof AccountManagementReportType) {
            throw new InvalidArgumentException('Expect instance of AccountManagementReportType');
        }

        return $this->accMngRepository->getReports($reportType->getPublisher(), $params, $this->defaultPageRange);
    }


    public function supportReport(ReportTypeInterface $reportType)
    {
        return $reportType instanceof AccountManagementReportType;
    }
}