<?php

namespace Tagcade\Service\Report\UnifiedReport\Selector;


use Knp\Component\Pager\Pagination\PaginationInterface;
use Tagcade\Model\Report\UnifiedReport\ReportType\ReportTypeInterface;

interface SelectorInterface {

    public function supportReport(ReportTypeInterface $reportType);

    /**
     * @param ReportTypeInterface $reportType
     * @param UnifiedReportParams $params
     * @return PaginationInterface
     */
    public function getReports(ReportTypeInterface $reportType, UnifiedReportParams $params);

} 