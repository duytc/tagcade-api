<?php
namespace Tagcade\Service\Report\UnifiedReport\Selector\Selectors\Network;


use DateTime;
use Tagcade\Exception\LogicException;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Model\Report\UnifiedReport\ReportType\Network\NetworkSubPublisher as NetworkSubPublisherReportType;
use Tagcade\Repository\Report\UnifiedReport\Publisher\SubPublisherNetworkReportRepositoryInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Params;
use Tagcade\Service\Report\UnifiedReport\Selector\Selectors\AbstractSelector;

class NetworkSubPublisher extends AbstractSelector
{
    /**
     * @var SubPublisherNetworkReportRepositoryInterface
     */
    protected $repository;

    /**
     * Network constructor.
     * @param SubPublisherNetworkReportRepositoryInterface $repository
     */
    public function __construct(SubPublisherNetworkReportRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param NetworkSubPublisherReportType $reportType
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return mixed
     */
    protected function doGetReports(NetworkSubPublisherReportType $reportType, DateTime $startDate, DateTime $endDate)
    {
        $reports = $this->repository->getReportFor($reportType->getSubPublisher(), $reportType->getAdNetwork(), $startDate, $endDate);
        if (is_array($reports)) {
            foreach($reports as $report) {
                $report->setName($reportType->getSubPublisher()->getUser()->getUsername());
            }
        }

        return $reports;
    }

    /**
     * @param ReportTypeInterface $reportType
     * @param Params $params
     * @return array
     */
    public function getDiscrepancy(ReportTypeInterface $reportType, Params $params)
    {
        if (!$reportType instanceof NetworkSubPublisherReportType) {
            throw new LogicException(sprintf('expect NetworkSubPublisherReportType object, %s given', get_class($reportType)));
        }

        return $this->repository->getDiscrepancy($reportType->getAdNetwork(), $params->getStartDate(), $params->getEndDate());
    }

    /**
     * @param ReportTypeInterface $reportType
     * @return bool
     */
    public function supportsReportType(ReportTypeInterface $reportType)
    {
        return $reportType instanceof NetworkSubPublisherReportType && !is_subclass_of($reportType , NetworkSubPublisherReportType::class);
    }
}