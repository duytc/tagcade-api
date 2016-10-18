<?php

namespace Tagcade\Service\Report\HeaderBiddingReport\Selector\Selectors\Hierarchy\Platform;

use DateTime;
use Tagcade\Service\Report\HeaderBiddingReport\Selector\Selectors\AbstractSelector;
use Tagcade\Repository\Report\HeaderBiddingReport\Hierarchy\Platform\AdTagReportRepositoryInterface;
use Tagcade\Model\Report\HeaderBiddingReport\ReportType\ReportTypeInterface;
use Tagcade\Model\Report\HeaderBiddingReport\ReportType\Hierarchy\Platform\AdTag as AdTagReportType;

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

    protected function doGetReports(AdTagReportType $reportType, DateTime $startDate, DateTime $endDate)
    {
        return $this->repository->getReportFor($reportType->getAdTag(), $startDate, $endDate);
    }

    public function supportsReportType(ReportTypeInterface $reportType)
    {
        return $reportType instanceof AdTagReportType;
    }
}