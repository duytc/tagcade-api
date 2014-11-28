<?php

namespace Tagcade\Domain\DTO\Statistics\Hierarchy\Platform;

use DateTime;
use Tagcade\Domain\DTO\Report\PerformanceReport\Display\Group\ReportGroup;
use Tagcade\Model\User\Role\PublisherInterface;

class AccountStatistics
{
    protected $report;

    function __construct(ReportGroup $report)
    {
        $this->report = $report;
    }

    /**
     * @return ReportGroup
     */
    public function getReport()
    {
        return $this->report;
    }
}