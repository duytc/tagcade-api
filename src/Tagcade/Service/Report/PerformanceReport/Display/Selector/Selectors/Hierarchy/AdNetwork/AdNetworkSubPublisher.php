<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Selector\Selectors\Hierarchy\AdNetwork;

use DateTime;
use Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\SubPublisher\SubPublisherAdNetworkReportRepositoryInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Selectors\AbstractSelector;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\AdNetwork\AdNetworkSubPublisher as AdNetworkSubPublisherReportType;

class AdNetworkSubPublisher extends AbstractSelector
{
    /**
     * @var SubPublisherAdNetworkReportRepositoryInterface
     */
    protected $repository;

    public function __construct(SubPublisherAdNetworkReportRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    protected function doGetReports(AdNetworkSubPublisherReportType $reportType, DateTime $startDate, DateTime $endDate, $queryParams = null)
    {
        $report = $this->repository->getReportFor($reportType->getSubPublisher(), $reportType->getAdNetwork(), $startDate, $endDate);
        if (is_array($report)) {
            foreach($report as $r) {
                $r->setName($reportType->getSubPublisher()->getUser()->getUsername());
            }
        }

        return $report;
    }

    public function supportsReportType(ReportTypeInterface $reportType)
    {
        return $reportType instanceof AdNetworkSubPublisherReportType;
    }
}