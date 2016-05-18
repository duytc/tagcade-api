<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Selector;

use DateTime;

interface ParamsInterface
{
    /**
     * @param DateTime $startDate
     * @param DateTime|null $endDate
     * @return $this
     */
    public function setDateRange(DateTime $startDate, DateTime $endDate = null);

    /**
     * @return DateTime
     */
    public function getStartDate();

    /**
     * @return DateTime
     */
    public function getEndDate();

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

    public function getQueryParams();

}