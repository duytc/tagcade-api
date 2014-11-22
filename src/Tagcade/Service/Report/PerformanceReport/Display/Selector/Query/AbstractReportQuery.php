<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Selector\Query;

use DateTime;

abstract class AbstractReportQuery implements ReportQueryInterface
{

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

    function __construct(DateTime $startDate, DateTime $endDate = null, $expanded = false, $grouped = false)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;

        $this->setExpanded($expanded);
        $this->setGrouped($grouped);
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
}