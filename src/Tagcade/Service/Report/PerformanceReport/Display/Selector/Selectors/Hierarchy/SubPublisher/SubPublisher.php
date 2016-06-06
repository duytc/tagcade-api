<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Selector\Selectors\Hierarchy\SubPublisher;

use DateTime;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Selectors\AbstractSelector;
use Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\SubPublisher\SubPublisherReportRepositoryInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\SubPublisher\SubPublisher as SubPublisherReportType;

class SubPublisher extends AbstractSelector
{
    /** @var SubPublisherReportRepositoryInterface */
    protected $repository;

    public function __construct(SubPublisherReportRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    protected function doGetReports(SubPublisherReportType $reportType, DateTime $startDate, DateTime $endDate)
    {
        $reports = $this->repository->getReportFor($reportType->getSubPublisher(), $startDate, $endDate);
        if (is_array($reports)) {
            foreach($reports as $report) {
                $report->setName($reportType->getSubPublisher()->getUser()->getUsername());
            }
        }

        return $reports;
    }

    public function supportsReportType(ReportTypeInterface $reportType)
    {
        return $reportType instanceof SubPublisherReportType;
    }
}