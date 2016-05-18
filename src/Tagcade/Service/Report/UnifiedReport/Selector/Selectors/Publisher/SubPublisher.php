<?php
namespace Tagcade\Service\Report\UnifiedReport\Selector\Selectors\Publisher;


use DateTime;
use Tagcade\Exception\LogicException;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Model\Report\UnifiedReport\ReportType\Publisher\SubPublisher as SubPublisherReportType;
use Tagcade\Repository\Report\UnifiedReport\Publisher\SubPublisherReportRepositoryInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Params;
use Tagcade\Service\Report\UnifiedReport\Selector\Selectors\AbstractSelector;

class SubPublisher extends AbstractSelector
{
    /**
     * @var SubPublisherReportRepositoryInterface
     */
    protected $repository;

    /**
     * Network constructor.
     * @param SubPublisherReportRepositoryInterface $repository
     */
    public function __construct(SubPublisherReportRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param SubPublisherReportType $reportType
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return mixed
     */
    protected function doGetReports(SubPublisherReportType $reportType, DateTime $startDate, DateTime $endDate)
    {
        return $this->repository->getReportFor($reportType->getSubPublisher(), $startDate, $endDate);
    }

    /**
     * @param ReportTypeInterface $reportType
     * @param Params $params
     * @return array
     */
    public function getDiscrepancy(ReportTypeInterface $reportType, Params $params)
    {
        if (!$reportType instanceof SubPublisherReportType) {
            throw new LogicException(sprintf('expect SubPublisherReportType object, %s given', get_class($reportType)));
        }

        return $this->repository->getDiscrepancy($reportType->getSubPublisher(), $params->getStartDate(), $params->getEndDate());
    }

    /**
     * @param ReportTypeInterface $reportType
     * @return bool
     */
    public function supportsReportType(ReportTypeInterface $reportType)
    {
        return $reportType instanceof SubPublisherReportType && !is_subclass_of($reportType, SubPublisherReportType::class);
    }
}