<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Grouper;

use Tagcade\Domain\DTO\Report\PerformanceReport\Display\Group\ReportGroup;
use Tagcade\Domain\DTO\Report\PerformanceReport\Display\Collection;

interface ReportGrouperInterface
{
    /**
     * @param Collection $reportCollection
     * @return ReportGroup
     */
    public function groupReports(Collection $reportCollection);
}
