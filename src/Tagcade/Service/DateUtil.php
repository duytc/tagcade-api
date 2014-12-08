<?php

namespace Tagcade\Service;

use DateTime;
use Tagcade\Exception\Report\InvalidDateException;
use DateInterval;
use DatePeriod;

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
        $today = new DateTime('today');

        return $today >= $startDate && $today <= $endDate;
    }

    public function isDateBeforeToday(DateTime $date)
    {
        return $date < (new DateTime('today'));
    }

    public function formatDate(DateTime $date)
    {
        return $date->format(self::DATE_FORMAT);
    }

    public function getFirstDateOfMonth()
    {
        return new DateTime(date('01-m-Y'));
    }

    public function dateDiffIncludeStartDate(DateTime $startDate, DateTime $endDate)
    {
        return $endDate->diff($startDate)->days + 1;
    }

    public function getLastDateOfMonth()
    {
        return new DateTime(date('t-m-Y'));
    }

    /**
     * @return int
     */
    public function getNumberOfRemainingDatesOfMonth()
    {
        $lastDate = $this->getLastDateOfMonth();

        return $lastDate->diff(new DateTime('today'))->days;
    }

    /**
     * @return int
     */
    public function getNumberOfDatesPassedOfMonth()
    {
        $today =  new DateTime('today');
        return $today->diff($this->getFirstDateOfMonth())->days;
    }

    /**
     * @return bool
     */
    public function isFirstDateOfMonth()
    {
        return $this->getNumberOfDatesPassedOfMonth() === 0;
    }


}