<?php

namespace Tagcade\Service\Report\UnifiedReport\Selector;


use Tagcade\Model\Report\UnifiedReport\ReportType\ReportTypeInterface;

interface SelectorInterface {

    public function supportReport(ReportTypeInterface $reportType);

    public function getReports(ReportTypeInterface $reportType, UnifiedReportParams $params);

} 