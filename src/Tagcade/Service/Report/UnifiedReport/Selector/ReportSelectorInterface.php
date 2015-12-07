<?php

namespace Tagcade\Service\Report\UnifiedReport\Selector;

use Tagcade\Model\Report\UnifiedReport\ReportType\ReportTypeInterface;

interface ReportSelectorInterface
{
    /**
     * @param $reportType
     * @param $params
     * @return mixed
     */
    public function getReports(ReportTypeInterface $reportType, UnifiedReportParams $params);
} 