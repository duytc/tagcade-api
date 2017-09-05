<?php

namespace Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform;

use Tagcade\Model\Report\PerformanceReport\Display\RootReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\SuperReportInterface;

interface PlatformReportInterface extends BillableInterface, RootReportInterface, CalculatedReportInterface, SuperReportInterface
{
    /**
     * @return self
     */
    public function calculateFinalOpportunityFillRate();
}
