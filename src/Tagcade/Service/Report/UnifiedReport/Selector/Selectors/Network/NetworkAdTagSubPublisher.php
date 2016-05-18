<?php
namespace Tagcade\Service\Report\UnifiedReport\Selector\Selectors\Network;


use DateTime;
use Tagcade\Exception\LogicException;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Repository\Report\UnifiedReport\Network\NetworkAdTagReportRepositoryInterface;
use Tagcade\Repository\Report\UnifiedReport\Network\NetworkAdTagSubPublisherReportRepositoryInterface;
use Tagcade\Model\Report\UnifiedReport\ReportType\Network\NetworkAdTagSubPublisher as NetworkAdTagSubPublisherReportType;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\Hierarchy\AdNetwork\AdNetworkInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Params;
use Tagcade\Service\Report\UnifiedReport\Selector\Selectors\AbstractSelector;

class NetworkAdTagSubPublisher extends AbstractSelector
{
    /**
     * @var NetworkAdTagReportRepositoryInterface
     */
    protected $repository;

    /**
     * Network constructor.
     * @param NetworkAdTagSubPublisherReportRepositoryInterface $repository
     */
    public function __construct(NetworkAdTagSubPublisherReportRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param NetworkAdTagSubPublisherReportType $reportType
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return mixed
     */
    protected function doGetReports(NetworkAdTagSubPublisherReportType $reportType, DateTime $startDate, DateTime $endDate)
    {
        return ($reportType->getAdNetwork() instanceof AdNetworkInterface)
            ? $this->repository->getReportFor($reportType->getAdNetwork(), $reportType->getPartnerTagId(), $reportType->getSubPublisherId() , $startDate, $endDate)
            : $this->repository->getReportForAllAdNetwork($reportType->getPartnerTagId(), $reportType->getSubPublisherId() , $startDate, $endDate);
    }

    /**
     * @param ReportTypeInterface $reportType
     * @param Params $params
     * @return array
     */
    public function getDiscrepancy(ReportTypeInterface $reportType, Params $params)
    {
        if (!$reportType instanceof NetworkAdTagSubPublisherReportType) {
            throw new LogicException(sprintf('expect NetworkAdTagSubPublisherReportType object, %s given', get_class($reportType)));
        }

        return $this->repository->getDiscrepancy($reportType->getAdNetwork(), $reportType->getPartnerTagId(), $params->getStartDate(), $params->getEndDate());
    }

    /**
     * @param ReportTypeInterface $reportType
     * @return bool
     */
    public function supportsReportType(ReportTypeInterface $reportType)
    {
        return $reportType instanceof NetworkAdTagSubPublisherReportType && !is_subclass_of($reportType, NetworkAdTagSubPublisherReportType::class);
    }
}