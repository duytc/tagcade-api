<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Creator\ReportType;

use Tagcade\Model\Core\AdTagInterface as AdTagModelInterface;
use Tagcade\Model\Report\PerformanceReport\Display\AdTagReportInterface;

interface AdTagInterface extends ReportTypeInterface
{
    /**
     * @param AdTagModelInterface $adTag
     * @return AdTagReportInterface
     */
    public function doCreateReport(AdTagModelInterface $adTag);
}