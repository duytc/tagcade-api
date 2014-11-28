<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Selector;

use DateTime;
use Tagcade\Exception\Report\InvalidDateException;

class Params implements ParamsInterface
{
    const PARAM_START_DATE = 'startDate';
    const PARAM_END_DATE = 'endDate';
    const PARAM_EXPAND = 'expand';
    const PARAM_GROUP = 'group';
    /**
     * @var DateTime
     */
    protected $startDate;
    /**
     * @var DateTime
     */
    protected $endDate;

    /**
     * @var bool
     */
    protected $expanded = false;

    /**
     * @var bool
     */
    protected $grouped = false;

    /**
     * @param DateTime $startDate
     * @param DateTime|null $endDate
     * @param bool $expanded Expand the results into their sub reports, i.e expand a site report into ad slot reports
     *                       This option has no effect if group is true, it will take priority
     * @param bool $grouped Group the results into one report with aggregated/averaged values
     */
    function __construct(DateTime $startDate, DateTime $endDate = null, $expanded = false, $grouped = false)
    {
        $this->setDateRange($startDate, $endDate);
        $this->setExpanded($expanded);
        $this->setGrouped($grouped);
    }

    /**
     * @inheritdoc
     */
    public function setDateRange(DateTime $startDate, DateTime $endDate = null)
    {
        if (!$endDate) {
            $endDate = $startDate;
        }

        if ($startDate > $endDate) {
            throw new InvalidDateException('start date must be before the end date');
        }

        $today = new DateTime('today');

        if ($startDate > $today || $endDate > $today) {
            throw new InvalidDateException('The date range cannot extend beyond today');
        }

        $this->startDate = $startDate;
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @inheritdoc
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @inheritdoc
     */
    public function setExpanded($expanded)
    {
        $this->expanded = (bool)$expanded;

        if ($this->expanded) {
            $this->grouped = false;
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getExpanded()
    {
        return $this->expanded;
    }

    /**
     * @inheritdoc
     */
    public function setGrouped($grouped)
    {
        $this->grouped = (bool)$grouped;

        if ($this->grouped) {
            $this->expanded = false;
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getGrouped()
    {
        return $this->grouped;
    }
}