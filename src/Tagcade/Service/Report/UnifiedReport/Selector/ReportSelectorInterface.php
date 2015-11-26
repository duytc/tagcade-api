<?php

namespace Tagcade\Service\Report\UnifiedReport\Selector;


interface ReportSelectorInterface
{
    /**
     * @param $reportType
     * @param $params
     * @return mixed
     */
    public function getReports($reportType, $params);
} 