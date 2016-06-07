<?php


namespace Tagcade\Service\Report\UnifiedReport\Selector\Selectors\Publisher;

use DateTime;
use Tagcade\Exception\LogicException;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Model\Report\UnifiedReport\ReportType\Publisher\SubPublisherNetwork as SubPublisherNetworkReportType;
use Tagcade\Repository\Report\UnifiedReport\Publisher\SubPublisherNetworkReportRepositoryInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Params;
use Tagcade\Service\Report\UnifiedReport\Selector\Selectors\AbstractSelector;

class SubPublisherNetwork extends AbstractSelector
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
     * @param SubPublisherNetworkReportType $reportType
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return mixed
     */
    protected function doGetReports(SubPublisherNetworkReportType $reportType, DateTime $startDate, DateTime $endDate)
    {
        $reports = $this->repository->getReportFor($reportType->getSubPublisher(), $reportType->getAdNetwork(), $startDate, $endDate);
        if (is_array($reports)) {
            foreach($reports as $report) {
                $report->setName($reportType->getAdNetwork()->getName());
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
        if (!$reportType instanceof SubPublisherNetworkReportType) {
            throw new LogicException(sprintf('expect SubPublisherNetworkReportType object, %s given', get_class($reportType)));
        }

        return $this->repository->getDiscrepancy($reportType->getSubPublisher(), $reportType->getAdNetwork(), $params->getStartDate(), $params->getEndDate());
    }

    /**
     * @param ReportTypeInterface $reportType
     * @return bool
     */
    public function supportsReportType(ReportTypeInterface $reportType)
    {
        return $reportType instanceof SubPublisherNetworkReportType && !is_subclass_of($reportType, SubPublisherNetworkReportType::class);
    }
}