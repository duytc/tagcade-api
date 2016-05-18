<?php

namespace Tagcade\Service\Report\UnifiedReport\Selector\Selectors\Network;


use DateTime;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Model\Report\UnifiedReport\ReportType\Network\NetworkDomainAdTagSubPublisher as NetworkDomainAdTagSubPublisherReportType;
use Tagcade\Repository\Report\UnifiedReport\Network\NetworkDomainAdTagSubPublisherReportRepositoryInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Params;
use Tagcade\Service\Report\UnifiedReport\Selector\Selectors\AbstractSelector;

class NetworkDomainAdTagSubPublisher extends AbstractSelector
{
    /**
     * @var NetworkDomainAdTagSubPublisherReportRepositoryInterface
     */
    protected $repository;

    /**
     * Network constructor.
     * @param NetworkDomainAdTagSubPublisherReportRepositoryInterface $repository
     */
    public function __construct(NetworkDomainAdTagSubPublisherReportRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param NetworkDomainAdTagSubPublisherReportType $reportType
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return mixed
     */
    protected function doGetReports(NetworkDomainAdTagSubPublisherReportType $reportType, DateTime $startDate, DateTime $endDate)
    {
        return $this->repository->getReportFor($reportType->getAdNetwork(), $reportType->getDomain(), $reportType->getPartnerTagId(), $reportType->getSubPublisher(), $startDate, $endDate);
    }

    /**
     * @param ReportTypeInterface $reportType
     * @param Params $params
     * @return array
     */
    public function getDiscrepancy(ReportTypeInterface $reportType, Params $params)
    {
        return [];
    }

    /**
     * @param ReportTypeInterface $reportType
     * @return bool
     */
    public function supportsReportType(ReportTypeInterface $reportType)
    {
        return $reportType instanceof NetworkDomainAdTagSubPublisherReportType && !is_subclass_of($reportType, NetworkDomainAdTagSubPublisherReportType::class);
    }
}