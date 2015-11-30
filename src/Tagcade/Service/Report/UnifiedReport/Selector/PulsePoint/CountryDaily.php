<?php

namespace Tagcade\Service\Report\UnifiedReport\Selector\PulsePoint;

use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Report\UnifiedReport\ReportType\ReportTypeInterface;
use Tagcade\Repository\Report\UnifiedReport\PulsePoint\CountryDailyRepositoryInterface;
use Tagcade\Service\Report\UnifiedReport\Selector\SelectorInterface;
use Tagcade\Service\Report\UnifiedReport\Selector\UnifiedReportParams;
use Tagcade\Model\Report\UnifiedReport\ReportType\PulsePoint\CountryDaily as CountryDailyReportType;

class CountryDaily implements SelectorInterface
{
    /**
     * @var CountryDailyRepositoryInterface
     */
    private $countryDailyRepository;

    function __construct(CountryDailyRepositoryInterface $countryDailyRepository)
    {
        $this->countryDailyRepository = $countryDailyRepository;
    }

    public function getReports(ReportTypeInterface $reportType, UnifiedReportParams $params)
    {
        if (!$reportType instanceof CountryDailyReportType) {
            throw new InvalidArgumentException('Expect instance of DomainImpressionReportType');
        }

        return $this->countryDailyRepository->getReportFor($reportType->getPublisher(), $params->getStartDate(), $params->getEndDate());
    }


    public function supportReport(ReportTypeInterface $reportType)
    {
        return $reportType instanceof CountryDailyReportType;
    }
}