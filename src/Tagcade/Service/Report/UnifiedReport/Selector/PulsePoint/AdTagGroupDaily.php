<?php

namespace Tagcade\Service\Report\UnifiedReport\Selector\PulsePoint;

use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Report\UnifiedReport\Pagination\CompoundResult;
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

        $averageValues = $this->accMngRepository->getAverageValuesForAdTagGroupDay($reportType->getPublisher(), $params);
        $items = $this->accMngRepository->getItemsForAdTagGroupDay($reportType->getPublisher(), $params, $this->defaultPageRange);
        $count = $this->accMngRepository->getCountForAdTagGroupDay($reportType->getPublisher(), $params);

        $pagination =  $this->paginator->paginate(
            new CompoundResult($items, $count)
        );

        return array(
            'pagination' => $pagination,
            'avg' => $averageValues
        );
    }


    public function supportReport(ReportTypeInterface $reportType)
    {
        return $reportType instanceof AdTagGroupDailyReportType;
    }
}