<?php

namespace Tagcade\Service\Report\SourceReport;

use DateTime;
use Tagcade\Model\Core\SiteInterface;

interface ReportSelectorInterface {

    /**
     * @param SiteInterface $site
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param int $rowOffset
     * @param int $rowLimit
     * @return array
     */
    public function getReports(SiteInterface $site, DateTime $startDate, DateTime $endDate, $rowOffset, $rowLimit);

} 