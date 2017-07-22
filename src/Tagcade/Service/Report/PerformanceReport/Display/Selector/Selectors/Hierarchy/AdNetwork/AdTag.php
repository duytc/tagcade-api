<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Selector\Selectors\Hierarchy\AdNetwork;

use DateTime;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\AdNetwork\AdTag as AdTagReportType;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\AdNetwork\AdTagReportRepositoryInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Selectors\AbstractSelector;

class AdTag extends AbstractSelector
{
    /**
     * @var AdTagReportRepositoryInterface
     */
    protected $repository;

    public function __construct(AdTagReportRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @inheritdoc
     */
    protected function doGetReports(ReportTypeInterface $reportType, DateTime $startDate, DateTime $endDate, $queryParams = null)
    {
        /** @var AdTagReportType $reportType */
        return $this->repository->getReportFor($reportType->getAdTag(), $startDate, $endDate);
    }

    /**
     * @inheritdoc
     */
    public function supportsReportType(ReportTypeInterface $reportType)
    {
        return $reportType instanceof AdTagReportType;
    }
}