<?php

namespace Tagcade\Service\Report\UnifiedReport\Selector\PulsePoint;

use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Report\UnifiedReport\Pagination\CompoundResult;
use Tagcade\Model\Report\UnifiedReport\ReportType\PulsePoint\AdTagGroupDaily as AdTagGroupDailyReportType;
use Tagcade\Model\Report\UnifiedReport\ReportType\ReportTypeInterface;
use Tagcade\Repository\Report\UnifiedReport\AbstractReportRepository;
use Tagcade\Service\Report\UnifiedReport\Selector\UnifiedReportParams;

class AdTagGroupDaily extends AccountManagement
{
    public function getReports(ReportTypeInterface $reportType, UnifiedReportParams $params)
    {
        if (!$reportType instanceof AdTagGroupDailyReportType) {
            throw new InvalidArgumentException('Expect instance of AdTagGroupDailyReportType');
        }

        return $this->accMngRepository->getReportsForAdTagGroupDay($reportType->getPublisher(), $params, $this->defaultPageRange);
    }


    public function supportReport(ReportTypeInterface $reportType)
    {
        return $reportType instanceof AdTagGroupDailyReportType;
    }
}