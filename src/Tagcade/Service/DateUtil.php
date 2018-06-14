<?php

namespace Tagcade\Service;

use DateTime;
use Tagcade\Exception\Report\InvalidDateException;

class DateUtil implements DateUtilInterface
{
    const DATE_FORMAT = 'Y-m-d';

    /**
     * @inheritdoc
     */
    public function getDateTime($dateString = null, $returnTodayIfEmpty = false)
    {
        if (empty($dateString)) {
            return $returnTodayIfEmpty ? new DateTime('today') : null;
        }

        if ($dateString instanceof DateTime) {
            return $dateString;
        }

        $validPattern = '#\d{4}-\d{2}-\d{2}#';

        if (!preg_match($validPattern, $dateString)) {
            throw new InvalidDateException('dateString must be a date in the format YYMMDD');
        }

        $dateTime = DateTime::createFromFormat(self::DATE_FORMAT, $dateString);
        $dateTime->setTime(0, 0, 0);

        return $dateTime;
    }

    public function isTodayInRange(DateTime $startDate, DateTime $endDate)
    {
        $today = (new DateTime('today'))->setTime(0, 0);
        $startDateClone = clone $startDate;
        $endDateClone = clone $endDate;

        return $today >= $startDateClone->setTime(0, 0) && $today <= $endDateClone->setTime(0, 0);
    }

    public function isOnlyTodayOrYesterdayInRange(DateTime $startDate, DateTime $endDate)
    {
        $today = (new DateTime('today'))->format('Y-m-d');
        $yesterday = (new DateTime('yesterday'))->format('Y-m-d');

        return $today == $startDate->format('Y-m-d') && $today == $endDate->format('Y-m-d')
               ||$yesterday == $startDate->format('Y-m-d') && $yesterday == $endDate->format('Y-m-d');
    }

    public function isToday(DateTime $startDate)
    {
        $today = (new DateTime('today'))->format('Y-m-d');

        return $today == $startDate->format('Y-m-d');
    }

    public function isYesterday(DateTime $endDate)
    {
        $yesterday = (new DateTime('yesterday'))->format('Y-m-d');

        return $yesterday == $endDate->format('Y-m-d');
    }

    public function isYesterdayInRange(DateTime $startDate, DateTime $endDate)
    {
        $yesterday = (new DateTime('yesterday'))->setTime(0, 0);
        $startDateClone = clone $startDate;
        $endDateClone = clone $endDate;

        return $yesterday >= $startDateClone->setTime(0, 0) && $yesterday <= $endDateClone->setTime(0, 0);
    }


    public function isDateBeforeToday(DateTime $date)
    {
        return $date < (new DateTime('today'));
    }

    public function formatDate(DateTime $date)
    {
        return $date->format(self::DATE_FORMAT);
    }

    public function getFirstDateInMonth(DateTime $date = null)
    {
        if (null === $date) {
            $date = new DateTime('today');
        }

        return new DateTime($date->format('1-m-Y'));
    }

    /**
     * @param DateTime $date
     * @param bool $forceEndOfMonth
     * @return DateTime end date of month this $date in when $forceEndOfMonth = true; otherwise return current $date
     */
    public function getLastDateInMonth(DateTime $date = null, $forceEndOfMonth = false)
    {
        if (null === $date) {
            $date = new DateTime('today');
        }

        $today = new DateTime('today');
        if ($today->format('m') === $date->format('m') && $forceEndOfMonth === false) {
            return $date;
        }

        return new DateTime($date->format('t-m-Y'));
    }

    /**
     * @return int
     */
    public function getNumberOfRemainingDatesInMonth()
    {
        $lastDate = $this->getLastDateInMonth(new DateTime('today'), true);

        return $lastDate->diff(new DateTime('today'))->days;
    }

    /**
     * @return int
     */
    public function getNumberOfDatesPassedInMonth()
    {
        $today = new DateTime('today');
        return $today->diff($this->getFirstDateInMonth())->days;
    }

    /**
     * @return bool
     */
    public function isFirstDateOfMonth()
    {
        return $this->getNumberOfDatesPassedInMonth() === 0;
    }
}