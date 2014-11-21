<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Selector\Query;

use DateTime;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;

interface ReportQueryInterface
{
    /**
     * @param bool $expanded
     * @return $this
     */
    public function setExpanded($expanded);

    /**
     * @return bool
     */
    public function getExpanded();

    /**
     * @param bool $grouped
     * @return $this
     */
    public function setGrouped($grouped);

    /**
     * @return bool
     */
    public function getGrouped();

    /**
     * @return DateTime
     */
    public function getStartDate();

    /**
     * @return DateTime
     */
    public function getEndDate();

    /**
     * @return ReportTypeInterface|ReportTypeInterface[]
     */
    public function getReportType();
}