<?php
namespace Tagcade\Service\Report\UnifiedReport\Selector\Selectors\Network;


use DateTime;
use Tagcade\Exception\LogicException;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Model\Report\UnifiedReport\ReportType\Network\NetworkSite as NetworkSiteReportType;
use Tagcade\Repository\Report\UnifiedReport\Network\NetworkSiteReportRepositoryInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Params;
use Tagcade\Service\Report\UnifiedReport\Selector\Selectors\AbstractSelector;

class NetworkSite extends AbstractSelector
{
    /**
     * @var NetworkSiteReportRepositoryInterface
     */
    protected $repository;

    /**
     * Network constructor.
     * @param NetworkSiteReportRepositoryInterface $repository
     */
    public function __construct(NetworkSiteReportRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param NetworkSiteReportType $reportType
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return mixed
     */
    protected function doGetReports(NetworkSiteReportType $reportType, DateTime $startDate, DateTime $endDate)
    {
        return $this->repository->getReportFor($reportType->getAdNetwork(), $reportType->getDomain(), $startDate, $endDate);
    }

    /**
     * @param ReportTypeInterface $reportType
     * @param Params $params
     * @return array
     */
    public function getDiscrepancy(ReportTypeInterface $reportType, Params $params)
    {
        if (!$reportType instanceof NetworkSiteReportType) {
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
        return $reportType instanceof NetworkSiteReportType && !is_subclass_of($reportType, NetworkSiteReportType::class);
    }
}