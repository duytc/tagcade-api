<?php

namespace Tagcade\Model\Report\RtbReport;

use DateTime;

interface ReportInterface extends ReportDataInterface
{
    /**
     * @return DateTime|null
     */
    public function getDate();

    /**
     * @param DateTime $date
     * @return self
     */
    public function setDate(DateTime $date);

    /**
     * @return string|null
     */
    public function getName();

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * @param int $opportunities
     * @return $this
     */
    public function setOpportunities($opportunities);

    /**
     * @param int $impressions
     * @return $this
     */
    public function setImpressions($impressions);

    /**
     * @return mixed
     */
    public function getEarnedAmount();

    /**
     * @param mixed $earnedAmount
     * @return self
     */
    public function setEarnedAmount($earnedAmount);

    /**
     * Sets all calculated fields
     * i.e fill rate
     */
    public function setCalculatedFields();

    /**
     *
     * @return $this
     */
    public function setFillRate();
}