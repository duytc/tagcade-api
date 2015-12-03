<?php

namespace Tagcade\Service\Report\UnifiedReport\Selector\PulsePoint;

use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Report\UnifiedReport\ReportType\PulsePoint\AdTagGroupDaily as AdTagGroupDailyReportType;
use Tagcade\Model\Report\UnifiedReport\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\UnifiedReport\Selector\UnifiedReportParams;

class AdTagGroupDaily extends AccountManagement
{
    public function getReports(ReportTypeInterface $reportType, UnifiedReportParams $params)
    {
        if (!$reportType instanceof AdTagGroupDailyReportType) {
            throw new InvalidArgumentException('Expect instance of AdTagGroupDailyReportType');
        }

        return $this->accMngRepository->getAdTagGroupDailyReportFor($reportType->getPublisher(), $params->getStartDate(), $params->getEndDate());
    }


    public function supportReport(ReportTypeInterface $reportType)
    {
        return $reportType instanceof AdTagGroupDailyReportType;
    }
}