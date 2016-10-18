<?php

namespace Tagcade\Service\Report\HeaderBiddingReport\Selector\Selectors\Hierarchy\Platform;

use DateTime;
use Tagcade\Service\Report\HeaderBiddingReport\Selector\Selectors\AbstractSelector;
use Tagcade\Repository\Report\HeaderBiddingReport\Hierarchy\Platform\SiteReportRepositoryInterface;
use Tagcade\Model\Report\HeaderBiddingReport\ReportType\ReportTypeInterface;
use Tagcade\Model\Report\HeaderBiddingReport\ReportType\Hierarchy\Platform\Site as SiteReportType;

class Site extends AbstractSelector
{
    /**
     * @var SiteReportRepositoryInterface
     */
    protected $repository;

    public function __construct(SiteReportRepositoryInterface $repository)
    {

        $this->repository = $repository;
    }

    protected function doGetReports(SiteReportType $reportType, DateTime $startDate, DateTime $endDate)
    {
        return $this->repository->getReportFor($reportType->getSite(), $startDate, $endDate);
    }

    public function supportsReportType(ReportTypeInterface $reportType)
    {
        return $reportType instanceof SiteReportType;
    }
}