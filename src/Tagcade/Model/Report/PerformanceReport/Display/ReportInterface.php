<?php

namespace Tagcade\Model\Report\PerformanceReport\Display;

use DateTime;

interface ReportInterface
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
     * @return int|null
     */
    public function getImpressions();

    /**
     * @return int|null
     */
    public function getPassbacks();

    /**
     * Sets all calculated fields
     * i.e fill rate
     */
    public function setCalculatedFields();
}