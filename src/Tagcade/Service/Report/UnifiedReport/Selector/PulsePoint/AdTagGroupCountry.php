<?php

namespace Tagcade\Service\Report\UnifiedReport\Selector\PulsePoint;

use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Report\UnifiedReport\Pagination\CompoundResult;
use Tagcade\Model\Report\UnifiedReport\ReportType\PulsePoint\AdTagGroupCountry as AdTagGroupCountryReportType;
use Tagcade\Model\Report\UnifiedReport\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\UnifiedReport\Selector\UnifiedReportParams;

class AdTagGroupCountry extends CountryDaily
{
    public function getReports(ReportTypeInterface $reportType, UnifiedReportParams $params)
    {
        if (!$reportType instanceof AdTagGroupCountryReportType) {
            throw new InvalidArgumentException('Expect instance of DomainImpressionReportType');
        }

        $averageValues = $this->countryDailyRepository->getAverageValuesForAdTagGroupCountry($reportType->getPublisher(), $params);

        $items = $this->countryDailyRepository->getItemsForAdTagGroupCountry($reportType->getPublisher(), $params, $this->defaultPageRange);
        $count = $this->countryDailyRepository->getCountForAdTagGroupCountry($reportType->getPublisher(), $params);

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
        return $reportType instanceof AdTagGroupCountryReportType;
    }
}