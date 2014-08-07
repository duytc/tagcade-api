<?php

namespace Tagcade\Model\Report\PerformanceReport\Behaviors;

trait HasSuperReport
{
    protected $superReport;

    /**
     * The super report is the report that 'owns' this report
     *
     * i.e a SiteReport owns many AdSlotReports
     */
    public function getSuperReport()
    {
        return $this->superReport;
    }
}