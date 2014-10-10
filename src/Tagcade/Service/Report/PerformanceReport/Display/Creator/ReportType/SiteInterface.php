<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Creator\ReportType;

use Tagcade\Model\Core\SiteInterface as SiteModelInterface;
use Tagcade\Model\Report\PerformanceReport\Display\SiteReportInterface;

interface SiteInterface extends ReportTypeInterface
{
    /**
     * @param SiteModelInterface $site
     * @return SiteReportInterface
     */
    public function doCreateReport(SiteModelInterface $site);
}