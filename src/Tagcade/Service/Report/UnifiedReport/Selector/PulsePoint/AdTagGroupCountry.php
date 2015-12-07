<?php

namespace Tagcade\Service\Report\UnifiedReport\Selector\PulsePoint;

use Tagcade\Exception\InvalidArgumentException;
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

        if ($params->getSize() > 0) {
            $pagination = $this->paginator->paginate(
                $this->countryDailyRepository->getQueryForPaginator($params), /* query NOT result */
                $params->getPage(),
                $params->getSize()
            );
        }
        else {
            $pagination = $this->paginator->paginate(
                $this->countryDailyRepository->getQueryForPaginator($params), /* query NOT result */
                $params->getPage()
            );
        }

        return $pagination;
    }


    public function supportReport(ReportTypeInterface $reportType)
    {
        return $reportType instanceof AdTagGroupCountryReportType;
    }
}