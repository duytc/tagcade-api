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

        $pageSize = $params->getSize() > 0 ? : $this->defaultPageRange;

        return $this->paginator->paginate(
            $this->countryDailyRepository->getQueryForPaginator($params),
            $params->getPage(),
            $pageSize
        );
    }

    public function supportReport(ReportTypeInterface $reportType)
    {
        return $reportType instanceof AdTagCountryReportType;
    }
}