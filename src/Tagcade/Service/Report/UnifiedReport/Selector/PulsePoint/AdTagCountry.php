<?php

namespace Tagcade\Service\Report\UnifiedReport\Selector\PulsePoint;

use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Report\UnifiedReport\Pagination\CompoundResult;
use Tagcade\Model\Report\UnifiedReport\ReportType\PulsePoint\AdTagCountry as AdTagCountryReportType;
use Tagcade\Model\Report\UnifiedReport\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\UnifiedReport\Selector\UnifiedReportParams;

class AdTagCountry extends CountryDaily
{
    public function getReports(ReportTypeInterface $reportType, UnifiedReportParams $params)
    {
        if (!$reportType instanceof AdTagCountryReportType) {
            throw new InvalidArgumentException('Expect instance of AdTagCountryReportType');
        }

        $averageValues = $this->countryDailyRepository->getAverageValuesForAdTagCountry($reportType->getPublisher(), $params);

        $items = $this->countryDailyRepository->getItemsForAdTagCountry($reportType->getPublisher(), $params, $this->defaultPageRange);
        $count = $this->countryDailyRepository->getCountForAdTagCountry($reportType->getPublisher(), $params);

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
        return $reportType instanceof AdTagCountryReportType;
    }
}