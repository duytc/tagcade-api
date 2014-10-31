<?php

namespace Tagcade\Repository\Report\SourceReport;

use DateTime;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\Report\SourceReport\Report as ReportModel;

interface ReportRepositoryInterface
{
    /**
     * Retrieved source reports between a date range
     *
     * @param SiteInterface $site
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return ReportModel[]
     */
    public function getReports(SiteInterface $site, DateTime $startDate, DateTime $endDate);

}