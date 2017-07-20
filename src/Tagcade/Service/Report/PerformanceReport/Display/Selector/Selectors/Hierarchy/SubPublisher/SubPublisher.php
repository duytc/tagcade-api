<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Selector\Selectors\Hierarchy\SubPublisher;

use DateTime;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\SubPublisher\SubPublisher as SubPublisherReportType;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\SubPublisher\SubPublisherReportRepositoryInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Selectors\AbstractSelector;

class SubPublisher extends AbstractSelector
{
    /** @var SubPublisherReportRepositoryInterface */
    protected $repository;

    public function __construct(SubPublisherReportRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @inheritdoc
     */
    protected function doGetReports(ReportTypeInterface $reportType, DateTime $startDate, DateTime $endDate, $queryParams = null)
    {
        /** @var SubPublisherReportType $reportType */
        $reports = $this->repository->getReportFor($reportType->getSubPublisher(), $startDate, $endDate);
        if (is_array($reports)) {
            foreach ($reports as $report) {
                $report->setName($reportType->getSubPublisher()->getUser()->getUsername());
            }
        }

        return $reports;
    }

    /**
     * @inheritdoc
     */
    public function supportsReportType(ReportTypeInterface $reportType)
    {
        return $reportType instanceof SubPublisherReportType;
    }
}