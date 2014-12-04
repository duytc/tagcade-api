<?php

namespace Tagcade\Service;

use DateTime;
use Tagcade\Exception\Report\InvalidDateException;

interface DateUtilInterface
{
    /**
     * Get a DateTime object
     * If $date is null, today's date is returned
     * The date format is Y-m-d i.e 2014-10-04
     *
     * @param int|DateTime|null $dateString
     * @param bool $returnTodayIfEmpty
     * @return DateTime
     * @throws InvalidDateException when an incorrect date format is supplied
     */
    public function getDateTime($dateString = null, $returnTodayIfEmpty = false);

    public function isTodayInRange(DateTime $startDate, DateTime $endDate);

    public function isDateBeforeToday(DateTime $date);

    public function formatDate(DateTime $date);

    public function getFirstDateOfMonth();

    public function dateDiffIncludeStartDate(DateTime $startDate, DateTime $endDate);

    public function getLastDateOfMonth();

    /**
     * @return int
     */
    public function getNumberOfRemainingDatesOfMonth();

    /**
     * @return int
     */
    public function getNumberOfDatesPassedOfMonth();
}