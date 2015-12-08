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

        $pageSize = $params->getSize() > 0 ? : $this->defaultPageRange;

        return $this->paginator->paginate(
            $this->accMngRepository->getQueryForPaginator($params),
            $params->getPage(),
            $pageSize
        );
    }


    public function supportReport(ReportTypeInterface $reportType)
    {
        return $reportType instanceof AdTagGroupDailyReportType;
    }
}