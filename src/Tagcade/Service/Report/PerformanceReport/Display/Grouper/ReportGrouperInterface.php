<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Grouper;

use Tagcade\Domain\DTO\Report\PerformanceReport\Display\Group\ReportGroup;
use Tagcade\Domain\DTO\Report\PerformanceReport\Display\ReportCollection;

interface ReportGrouperInterface
{
    /**
     * @param ReportCollection $reportCollection
     * @return ReportGroup
     */
    public function groupReports(ReportCollection $reportCollection);
}
