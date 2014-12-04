<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Selector\Grouper;

use Tagcade\Service\Report\PerformanceReport\Display\Selector\Result\ReportCollectionInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Result\Group\ReportGroup;

interface ReportGrouperInterface
{
    /**
     * @param ReportCollectionInterface $reportCollection
     * @return ReportGroup
     */
    public function groupReports(ReportCollectionInterface $reportCollection);
}
