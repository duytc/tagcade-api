<?php
namespace Tagcade\Service\Report\UnifiedReport\Selector\Selectors\Network;


use DateTime;
use Tagcade\Exception\LogicException;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Model\Report\UnifiedReport\ReportType\Network\NetworkSiteSubPublisher as NetworkSiteSubPublisherReportType;
use Tagcade\Repository\Report\UnifiedReport\Network\NetworkSiteSubPublisherReportRepositoryInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Params;
use Tagcade\Service\Report\UnifiedReport\Selector\Selectors\AbstractSelector;

class NetworkSiteSubPublisher extends AbstractSelector
{
    /**
     * @var NetworkSiteSubPublisherReportRepositoryInterface
     */
    protected $repository;

    /**
     * Network constructor.
     * @param NetworkSiteSubPublisherReportRepositoryInterface $repository
     */
    public function __construct(NetworkSiteSubPublisherReportRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param NetworkSiteSubPublisherReportType $reportType
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return mixed
     */
    protected function doGetReports(NetworkSiteSubPublisherReportType $reportType, DateTime $startDate, DateTime $endDate)
    {
        return $reportType->getAdNetwork() instanceof AdNetworkInterface
            ? $this->repository->getReportFor($reportType->getAdNetwork(), $reportType->getDomain(), $reportType->getSubPublisher() , $startDate, $endDate)
            : $this->repository->getReportForAllAdNetwork($reportType->getDomain(), $reportType->getSubPublisher() , $startDate, $endDate);
    }

    /**
     * @param ReportTypeInterface $reportType
     * @param Params $params
     * @return array
     */
    public function getDiscrepancy(ReportTypeInterface $reportType, Params $params)
    {
        if (!$reportType instanceof NetworkSiteSubPublisherReportType) {
            throw new LogicException(sprintf('expect NetworkSiteReportType object, %s given', get_class($reportType)));
        }

        return $this->repository->getDiscrepancy($reportType->getAdNetwork(),$reportType->getDomain(), $params->getStartDate(), $params->getEndDate());
    }

    /**
     * @param ReportTypeInterface $reportType
     * @return bool
     */
    public function supportsReportType(ReportTypeInterface $reportType)
    {
        return $reportType instanceof NetworkSiteSubPublisherReportType && !is_subclass_of($reportType, NetworkSiteSubPublisherReportType::class);
    }
}