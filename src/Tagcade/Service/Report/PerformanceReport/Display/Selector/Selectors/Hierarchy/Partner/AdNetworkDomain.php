<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Selector\Selectors\Hierarchy\Partner;

use DateTime;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Partner\AdNetworkDomain as PartnerAdNetworkDomainReportType;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\Partner\AdNetworkDomainReportRepositoryInterface as PartnerAdNetworkDomainReportRepositoryInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Selectors\AbstractSelector;

class AdNetworkDomain extends AbstractSelector
{
    /** @var PartnerAdNetworkDomainReportRepositoryInterface */
    protected $repository;

    public function __construct(PartnerAdNetworkDomainReportRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    protected function doGetReports(PartnerAdNetworkDomainReportType $reportType, DateTime $startDate, DateTime $endDate)
    {
        return $reportType->getAdNetwork() instanceof AdNetworkInterface
            ? $this->repository->getReportFor($reportType->getAdNetwork(), $reportType->getDomain(), $startDate, $endDate)
            : $this->repository->getSiteReportForAllPartners($reportType->getDomain(), $startDate, $endDate);
    }

    public function supportsReportType(ReportTypeInterface $reportType)
    {
        return $reportType instanceof PartnerAdNetworkDomainReportType;
    }
}