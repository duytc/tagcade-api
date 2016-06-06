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
    const PARAM_SUB_BREAKDOWN = 'subBreakDown';

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
     * @todo remove this property, it is no longer used
     */
    protected $expanded = false;

    /**
     * @var bool
     */
    protected $grouped = false;

    /**
     * @var null
     */
    private $queryParams;

    /**
     * @param DateTime $startDate
     * @param DateTime|null $endDate
     * @param bool $expanded Expand the results into their sub reports, i.e expand a site report into ad slot reports
     *                       This option has no effect if group is true, it will take priority
     * @param bool $grouped Group the results into one report with aggregated/averaged values
     * @param null $queryParams
     * @throws \Exception
     */
    function __construct(DateTime $startDate, DateTime $endDate = null, $expanded = false, $grouped = false, $queryParams = null)
    {
        $this->setDateRange($startDate, $endDate);
        $this->setExpanded($expanded);
        $this->setGrouped($grouped);

        if ($queryParams != null && !is_array($queryParams)) {
            throw new \Exception('Expect array to be query params');
        }

        $this->queryParams = $queryParams;
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

    /**
     * @return array
     */
    public function getQueryParams()
    {
        return $this->queryParams;
    }

    public function addParam($key, $value)
    {
        if ($this->queryParams == null) {
            $this->queryParams = [];
        }

        $this->queryParams[$key] = $value;

        return $this->queryParams;
    }

}