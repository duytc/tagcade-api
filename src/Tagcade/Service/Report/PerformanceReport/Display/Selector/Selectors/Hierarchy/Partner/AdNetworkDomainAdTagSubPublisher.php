<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Selector\Selectors\Hierarchy\Partner;

use DateTime;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Partner\AdNetworkDomainAdTagSubPublisher as PartnerAdNetworkDomainAdTagSubPublisherReportType;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\Partner\AdNetworkDomainAdTagSubPublisherReportRepositoryInterface as PartnerAdNetworkDomainAdTagSubPublisherReportRepositoryInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Selectors\AbstractSelector;

class AdNetworkDomainAdTagSubPublisher extends AbstractSelector
{
    /** @var PartnerAdNetworkDomainAdTagSubPublisherReportRepositoryInterface */
    protected $repository;

    public function __construct(PartnerAdNetworkDomainAdTagSubPublisherReportRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    protected function doGetReports(PartnerAdNetworkDomainAdTagSubPublisherReportType $reportType, DateTime $startDate, DateTime $endDate)
    {
        return $this->repository->getReportFor($reportType->getAdNetwork(), $reportType->getDomain(), $reportType->getPartnerTagId(), $reportType->getSubPublisher(), $startDate, $endDate);
    }

    public function supportsReportType(ReportTypeInterface $reportType)
    {
        return $reportType instanceof PartnerAdNetworkDomainAdTagSubPublisherReportType;
    }
}