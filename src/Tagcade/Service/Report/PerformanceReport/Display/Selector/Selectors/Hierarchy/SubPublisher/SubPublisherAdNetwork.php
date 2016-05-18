<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Selector\Selectors\Hierarchy\SubPublisher;

use DateTime;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Selectors\AbstractSelector;
use Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\SubPublisher\SubPublisherAdNetworkReportRepositoryInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\SubPublisher\SubPublisherAdNetwork as SubPublisherAdNetworkReportType;

class SubPublisherAdNetwork extends AbstractSelector
{
    /** @var SubPublisherAdNetworkReportRepositoryInterface */
    protected $repository;

    public function __construct(SubPublisherAdNetworkReportRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    protected function doGetReports(SubPublisherAdNetworkReportType $reportType, DateTime $startDate, DateTime $endDate)
    {
        return $this->repository->getReportFor($reportType->getSubPublisher(), $reportType->getAdNetwork(), $startDate, $endDate);
    }

    public function supportsReportType(ReportTypeInterface $reportType)
    {
        return $reportType instanceof SubPublisherAdNetworkReportType;
    }
}