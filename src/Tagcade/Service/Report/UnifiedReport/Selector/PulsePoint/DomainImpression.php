<?php

namespace Tagcade\Service\Report\UnifiedReport\Selector\PulsePoint;

use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Report\UnifiedReport\ReportType\ReportTypeInterface;
use Tagcade\Repository\Report\UnifiedReport\PulsePoint\DomainReportRepositoryInterface;
use Tagcade\Service\Report\UnifiedReport\Selector\SelectorInterface;
use Tagcade\Service\Report\UnifiedReport\Selector\UnifiedReportParams;
use Tagcade\Model\Report\UnifiedReport\ReportType\PulsePoint\DomainImpression as DomainImpressionReportType;

class DomainImpression implements SelectorInterface
{
    /**
     * @var DomainReportRepositoryInterface
     */
    private $domainReportRepository;

    function __construct(DomainReportRepositoryInterface $domainReportRepository)
    {
        $this->domainReportRepository = $domainReportRepository;
    }

    public function getReports(ReportTypeInterface $reportType, UnifiedReportParams $params)
    {
        if (!$reportType instanceof DomainImpressionReportType) {
            throw new InvalidArgumentException('Expect instance of DomainImpressionReportType');
        }

        return $this->domainReportRepository->getReportFor($reportType->getPublisher(), $params->getStartDate(), $params->getEndDate());
    }


    public function supportReport(ReportTypeInterface $reportType)
    {
        return $reportType instanceof DomainImpressionReportType;
    }
}