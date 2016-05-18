<?php
namespace Tagcade\Service\Report\UnifiedReport\Selector\Selectors\Publisher;


use DateTime;
use Tagcade\Exception\LogicException;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Model\Report\UnifiedReport\ReportType\Publisher\Publisher as PublisherReportType;
use Tagcade\Repository\Report\UnifiedReport\Publisher\PublisherReportRepositoryInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Params;
use Tagcade\Service\Report\UnifiedReport\Selector\Selectors\AbstractSelector;

class Publisher extends AbstractSelector
{
    /**
     * @var PublisherReportRepositoryInterface
     */
    protected $repository;

    /**
     * Network constructor.
     * @param PublisherReportRepositoryInterface $repository
     */
    public function __construct(PublisherReportRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param PublisherReportType $reportType
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return mixed
     */
    protected function doGetReports(PublisherReportType $reportType, DateTime $startDate, DateTime $endDate)
    {
        return $this->repository->getReportFor($reportType->getPublisher(), $startDate, $endDate);
    }

    /**
     * @param ReportTypeInterface $reportType
     * @param Params $params
     * @return array
     */
    public function getDiscrepancy(ReportTypeInterface $reportType, Params $params)
    {
        if (!$reportType instanceof PublisherReportType) {
            throw new LogicException(sprintf('expect PublisherReportType object, %s given', get_class($reportType)));
        }

        return $this->repository->getDiscrepancy($reportType->getPublisher(), $params->getStartDate(), $params->getEndDate());
    }

    /**
     * @param ReportTypeInterface $reportType
     * @return bool
     */
    public function supportsReportType(ReportTypeInterface $reportType)
    {
        return $reportType instanceof PublisherReportType && !is_subclass_of($reportType, PublisherReportType::class);
    }
}