<?php
namespace Tagcade\Service\Report\UnifiedReport\Selector\Selectors\Network;


use DateTime;
use Tagcade\Exception\LogicException;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Model\Report\UnifiedReport\ReportType\Network\NetworkAdTag as NetworkAdTagReportType;
use Tagcade\Repository\Report\UnifiedReport\Network\NetworkAdTagReportRepositoryInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Params;
use Tagcade\Service\Report\UnifiedReport\Selector\Selectors\AbstractSelector;

class NetworkAdTag extends AbstractSelector
{
    /** @var NetworkAdTagReportRepositoryInterface */
    protected $repository;

    /**
     * Network constructor.
     * @param NetworkAdTagReportRepositoryInterface $repository
     */
    public function __construct(NetworkAdTagReportRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param NetworkAdTagReportType $reportType
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return mixed
     */
    protected function doGetReports(NetworkAdTagReportType $reportType, DateTime $startDate, DateTime $endDate)
    {
        return $this->repository->getReportFor($reportType->getPublisher(), $reportType->getAdNetwork(), $reportType->getPartnerTagId(), $startDate, $endDate);
    }

    /**
     * @param ReportTypeInterface $reportType
     * @param Params $params
     * @return array
     */
    public function getDiscrepancy(ReportTypeInterface $reportType, Params $params)
    {
        if (!$reportType instanceof NetworkAdTagReportType) {
            throw new LogicException(sprintf('expect NetworkAdTagReportType object, %s given', get_class($reportType)));
        }

        return $this->repository->getDiscrepancy($reportType->getAdNetwork(), $reportType->getPartnerTagId(), $params->getStartDate(), $params->getEndDate());
    }

    /**
     * @param ReportTypeInterface $reportType
     * @return bool
     */
    public function supportsReportType(ReportTypeInterface $reportType)
    {
        return $reportType instanceof NetworkAdTagReportType && !is_subclass_of($reportType, NetworkAdTagReportType::class);
    }
}