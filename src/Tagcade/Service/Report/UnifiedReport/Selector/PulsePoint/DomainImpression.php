<?php

namespace Tagcade\Service\Report\UnifiedReport\Selector\PulsePoint;

use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Report\UnifiedReport\ReportType\PulsePoint\DomainImpression as DomainImpressionReportType;
use Tagcade\Model\Report\UnifiedReport\ReportType\ReportTypeInterface;
use Tagcade\Repository\Report\UnifiedReport\PulsePoint\DomainReportRepositoryInterface;
use Tagcade\Service\Report\UnifiedReport\Selector\SelectorInterface;
use Tagcade\Service\Report\UnifiedReport\Selector\UnifiedReportParams;

class DomainImpression implements SelectorInterface
{
    protected $defaultPageRange;
    /**
     * @var DomainReportRepositoryInterface
     */
    private $domainReportRepository;

    function __construct(DomainReportRepositoryInterface $domainReportRepository, $defaultPageRange)
    {
        $this->domainReportRepository = $domainReportRepository;
        $this->defaultPageRange = $defaultPageRange;
    }

    public function getReports(ReportTypeInterface $reportType, UnifiedReportParams $params)
    {
        if (!$reportType instanceof DomainImpressionReportType) {
            throw new InvalidArgumentException('Expect instance of DomainImpressionReportType');
        }

        return $this->domainReportRepository->getReports($reportType->getPublisher(), $params, $this->defaultPageRange);
    }


    public function supportReport(ReportTypeInterface $reportType)
    {
        return $reportType instanceof DomainImpressionReportType;
    }
}