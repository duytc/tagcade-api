<?php

namespace Tagcade\Service\Report\UnifiedReport\Selector\Selectors\Network;


use DateTime;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Repository\Report\UnifiedReport\Network\NetworkDomainAdTagReportRepositoryInterface;
use Tagcade\Model\Report\UnifiedReport\ReportType\Network\NetworkDomainAdTag as NetworkDomainAdTagReportType;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Params;
use Tagcade\Service\Report\UnifiedReport\Selector\Selectors\AbstractSelector;

class NetworkDomainAdTag extends AbstractSelector
{
    /**
     * @var NetworkDomainAdTagReportRepositoryInterface
     */
    protected $repository;

    /**
     * Network constructor.
     * @param NetworkDomainAdTagReportRepositoryInterface $repository
     */
    public function __construct(NetworkDomainAdTagReportRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param NetworkDomainAdTagReportType $reportType
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return mixed
     */
    protected function doGetReports(NetworkDomainAdTagReportType $reportType, DateTime $startDate, DateTime $endDate)
    {
        return $this->repository->getReportFor($reportType->getAdNetwork(), $reportType->getDomain(), $reportType->getPartnerTagId(), $startDate, $endDate);
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
        return $reportType instanceof NetworkDomainAdTagReportType && !is_subclass_of($reportType, NetworkDomainAdTagReportType::class);
    }
}