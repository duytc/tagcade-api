<?php

namespace Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform;

use Tagcade\Model\Report\PerformanceReport\Display\BaseAdTagReportInterface;

interface AdTagReportInterface extends BaseAdTagReportInterface
{
    /**
     * To calculate the relative fill rate, the total opportunities from the entire ad slot must be supplied
     *
     * @param int $totalOpportunities
     * @return $this
     */
    public function setRelativeFillRate($totalOpportunities);
}