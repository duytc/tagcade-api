<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Selector\Selectors\Hierarchy\AdNetwork;

use DateTime;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Selectors\AbstractSelector;
use Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\AdNetwork\AdNetworkReportRepositoryInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\AdNetwork\AdNetwork as AdNetworkReportType;

class AdNetwork extends AbstractSelector
{
    /**
     * @var AdNetworkReportRepositoryInterface
     */
    protected $repository;

    public function __construct(AdNetworkReportRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    protected function doGetReports(AdNetworkReportType $reportType, DateTime $startDate, DateTime $endDate, $queryParams = null)
    {
        // partner report
        if (is_array($queryParams) && array_key_exists('partner', $queryParams) && $queryParams['partner'] == 'all' && array_key_exists('publisher', $queryParams)) {
            return $this->repository->getPublisherAllPartnersByDay($queryParams['publisher'], $startDate, $endDate);
        }

        $adNetwork = $reportType->getAdNetwork();
        return ($adNetwork instanceof AdNetworkInterface)
            ? $this->repository->getReportFor($adNetwork, $startDate, $endDate)
            : $this->repository->getReportForAllAdNetworkOfPublisher($reportType->getPublisher(), $startDate, $endDate);
    }

    public function supportsReportType(ReportTypeInterface $reportType)
    {
        return $reportType instanceof AdNetworkReportType;
    }
}