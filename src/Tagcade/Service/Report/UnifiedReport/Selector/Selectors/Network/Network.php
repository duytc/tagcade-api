<?php
namespace Tagcade\Service\Report\UnifiedReport\Selector\Selectors\Network;


use DateTime;
use Tagcade\Exception\LogicException;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Repository\Report\UnifiedReport\Network\NetworkReportRepositoryInterface;
use Tagcade\Model\Report\UnifiedReport\ReportType\Network\Network as NetworkReportType;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Params;
use Tagcade\Service\Report\UnifiedReport\Selector\Selectors\AbstractSelector;

class Network extends AbstractSelector
{
    /**
     * @var NetworkReportRepositoryInterface
     */
    protected $repository;

    /**
     * Network constructor.
     * @param NetworkReportRepositoryInterface $repository
     */
    public function __construct(NetworkReportRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param NetworkReportType $reportType
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return mixed
     */
    protected function doGetReports(NetworkReportType $reportType, DateTime $startDate, DateTime $endDate)
    {
        return $this->repository->getReportFor($reportType->getAdNetwork(), $startDate, $endDate);
    }

    /**
     * @param ReportTypeInterface $reportType
     * @param Params $params
     * @return array
     */
    public function getDiscrepancy(ReportTypeInterface $reportType, Params $params)
    {
        if (!$reportType instanceof NetworkReportType) {
            throw new LogicException(sprintf('expect AdNetworkReportType object, %s given', get_class($reportType)));
        }

        return $this->repository->getDiscrepancy($reportType->getAdNetwork(), $params->getStartDate(), $params->getEndDate());
    }

    /**
     * @param ReportTypeInterface $reportType
     * @return bool
     */
    public function supportsReportType(ReportTypeInterface $reportType)
    {
        return $reportType instanceof NetworkReportType && !is_subclass_of($reportType, NetworkReportType::class);
    }
}