<?php

namespace Tagcade\Service\Report\UnifiedReport\Selector\PulsePoint;

use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Report\UnifiedReport\ReportType\PulsePoint\AdTagCountry as AdTagCountryReportType;
use Tagcade\Model\Report\UnifiedReport\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\UnifiedReport\Selector\UnifiedReportParams;

class AdTagCountry extends CountryDaily
{
    public function getReports(ReportTypeInterface $reportType, UnifiedReportParams $params)
    {
        if (!$reportType instanceof AdTagCountryReportType) {
            throw new InvalidArgumentException('Expect instance of AdTagDomainReportType');
        }

        return $this->countryDailyRepository->getAdTagCountryReportFor($reportType->getPublisher(), $params->getStartDate(), $params->getEndDate());
    }

    public function supportReport(ReportTypeInterface $reportType)
    {
        return $reportType instanceof AdTagCountryReportType;
    }
}