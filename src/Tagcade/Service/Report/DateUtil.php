<?php

namespace Tagcade\Service\Report;

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
}