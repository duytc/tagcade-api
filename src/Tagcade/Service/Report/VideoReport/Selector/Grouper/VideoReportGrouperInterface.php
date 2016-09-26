<?php

namespace Tagcade\Service\Report\VideoReport\Selector\Grouper;

use Tagcade\Service\Report\VideoReport\Selector\Result\Group\ReportGroup;
use Tagcade\Service\Report\VideoReport\Selector\Result\ReportResultInterface;

interface VideoReportGrouperInterface
{
    /**
     * @param ReportResultInterface $reportCollection
     * @return ReportGroup
     */
    public function groupReports(ReportResultInterface $reportCollection);
}
