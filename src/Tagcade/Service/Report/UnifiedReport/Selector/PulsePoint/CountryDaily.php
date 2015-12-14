<?php

namespace Tagcade\Service\Report\UnifiedReport\Selector\PulsePoint;

use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Report\UnifiedReport\ReportType\PulsePoint\CountryDaily as CountryDailyReportType;
use Tagcade\Model\Report\UnifiedReport\ReportType\ReportTypeInterface;
use Tagcade\Repository\Report\UnifiedReport\PulsePoint\CountryDailyRepositoryInterface;
use Tagcade\Service\Report\UnifiedReport\Selector\SelectorInterface;
use Tagcade\Service\Report\UnifiedReport\Selector\UnifiedReportParams;

class CountryDaily implements SelectorInterface
{
    protected $defaultPageRange;
    /**
     * @var CountryDailyRepositoryInterface
     */
    protected $countryDailyRepository;

    function __construct(CountryDailyRepositoryInterface $countryDailyRepository, $defaultPageRange)
    {
        $this->countryDailyRepository = $countryDailyRepository;
        $this->defaultPageRange = $defaultPageRange;
    }

    public function getReports(ReportTypeInterface $reportType, UnifiedReportParams $params)
    {
        if (!$reportType instanceof CountryDailyReportType) {
            throw new InvalidArgumentException('Expect instance of DomainImpressionReportType');
        }

        return $this->countryDailyRepository->getReports($reportType->getPublisher(), $params, $this->defaultPageRange);
    }


    public function supportReport(ReportTypeInterface $reportType)
    {
        return $reportType instanceof CountryDailyReportType;
    }
}