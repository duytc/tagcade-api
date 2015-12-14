<?php

namespace Tagcade\Service\Report\UnifiedReport\Selector\PulsePoint;

use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Report\UnifiedReport\ReportType\PulsePoint\AdTagDomainImpression as AdTagDomainImpressionReportType;
use Tagcade\Model\Report\UnifiedReport\ReportType\ReportTypeInterface;
use Tagcade\Repository\Report\UnifiedReport\PulsePoint\AdTagDomainImpressionRepositoryInterface;
use Tagcade\Service\Report\UnifiedReport\Selector\SelectorInterface;
use Tagcade\Service\Report\UnifiedReport\Selector\UnifiedReportParams;

class AdTagDomainImpression implements SelectorInterface
{
    /**
     * @var AdTagDomainImpressionRepositoryInterface
     */
    private $adTagDomainImpRepository;
    /**
     * @var
     */
    private $defaultPageRange;

    function __construct(AdTagDomainImpressionRepositoryInterface $adTagDomainImpRepository, $defaultPageRange)
    {
        $this->adTagDomainImpRepository = $adTagDomainImpRepository;
        $this->defaultPageRange = $defaultPageRange;
    }

    public function getReports(ReportTypeInterface $reportType, UnifiedReportParams $params)
    {
        if (!$reportType instanceof AdTagDomainImpressionReportType) {
            throw new InvalidArgumentException('Expect instance of DomainImpressionReportType');
        }

        return $this->adTagDomainImpRepository->getReports($reportType->getPublisher(), $params, $this->defaultPageRange);
    }


    public function supportReport(ReportTypeInterface $reportType)
    {
        return $reportType instanceof AdTagDomainImpressionReportType;
    }
}