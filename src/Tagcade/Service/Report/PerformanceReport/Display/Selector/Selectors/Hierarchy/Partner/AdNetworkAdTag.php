<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Selector\Selectors\Hierarchy\Partner;

use DateTime;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Partner\AdNetworkAdTag as PartnerAdNetworkAdTagReportType;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\Partner\AdNetworkAdTagReportRepositoryInterface as PartnerAdNetworkAdTagReportRepositoryInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Selectors\AbstractSelector;

class AdNetworkAdTag extends AbstractSelector
{
    /** @var PartnerAdNetworkAdTagReportRepositoryInterface */
    protected $repository;

    public function __construct(PartnerAdNetworkAdTagReportRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    protected function doGetReports(PartnerAdNetworkAdTagReportType $reportType, DateTime $startDate, DateTime $endDate)
    {
        return $this->repository->getReportFor($reportType->getAdNetwork(), $reportType->getPartnerTagId(), $startDate, $endDate);
    }

    public function supportsReportType(ReportTypeInterface $reportType)
    {
        return $reportType instanceof PartnerAdNetworkAdTagReportType;
    }
}