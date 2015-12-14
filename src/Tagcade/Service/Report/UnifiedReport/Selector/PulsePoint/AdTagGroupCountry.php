<?php

namespace Tagcade\Service\Report\UnifiedReport\Selector\PulsePoint;

use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Report\UnifiedReport\Pagination\CompoundResult;
use Tagcade\Model\Report\UnifiedReport\ReportType\PulsePoint\AdTagGroupCountry as AdTagGroupCountryReportType;
use Tagcade\Model\Report\UnifiedReport\ReportType\ReportTypeInterface;
use Tagcade\Repository\Report\UnifiedReport\AbstractReportRepository;
use Tagcade\Service\Report\UnifiedReport\Selector\UnifiedReportParams;

class AdTagGroupCountry extends CountryDaily
{
    public function getReports(ReportTypeInterface $reportType, UnifiedReportParams $params)
    {
        if (!$reportType instanceof AdTagGroupCountryReportType) {
            throw new InvalidArgumentException('Expect instance of DomainImpressionReportType');
        }

        return $this->countryDailyRepository->getReportsForAdTagGroupCountry($reportType->getPublisher(), $params, $this->defaultPageRange);
    }


    public function supportReport(ReportTypeInterface $reportType)
    {
        return $reportType instanceof AdTagGroupCountryReportType;
    }
}