<?php

namespace Tagcade\Service\Reporting;

use DateTime;
use Tagcade\Exception\Report\InvalidDateException;

class ReportUtil implements ReportUtilInterface
{
    const DATE_FORMAT = 'ymd';

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

        $validPattern = '#\d{6}#';

        if (!preg_match($validPattern, $dateString)) {
            throw new InvalidDateException('dateString must be a date in the format YYMMDD');
        }

        $dateTime = DateTime::createFromFormat(self::DATE_FORMAT, $dateString);
        $dateTime->setTime(0, 0, 0);

        return $dateTime;
    }

    public function getDateRangeMetaFromStr($startDate, $endDate = null)
    {
        $startDate = $this->getDateFromStr($startDate);

        if (null !== $endDate) {
            $endDate = $this->getDateFromStr($endDate);
        }

        return $this->getDateRangeMeta($startDate, $endDate);
    }

    public function getDateRangeMeta(DateTime $startDate, DateTime $endDate = null)
    {
        if (!$startDate instanceof DateTime) {
            throw new \Exception('startDate must be a datetime');
        }

        if (null !== $endDate && !$endDate instanceof DateTime) {
            throw new \Exception('endDate must be a datetime');
        }

        if (null == $endDate) {
            $endDate = $startDate;
        }

        $todayIncludedInRange = false;
        $dateToday = new DateTime('today');
        $dateDiffTo = $endDate->diff($dateToday, true);

        if ($dateDiffTo->d == 0) {
            $todayIncludedInRange = true;
        }

        $isDateRange = $startDate !== $endDate;

        return array(
            'start_date'        => $startDate,
            'end_date'          => $endDate,
            'start_date_str'    => $startDate->format(self::DATE_FORMAT),
            'end_date_str'      => $endDate->format(self::DATE_FORMAT),
            'is_date_range'     => $isDateRange,
            'today_included_in_range' => $todayIncludedInRange,
        );
    }

    public function formatDate(DateTime $date)
    {
        return $date->format(self::DATE_FORMAT);
    }
}