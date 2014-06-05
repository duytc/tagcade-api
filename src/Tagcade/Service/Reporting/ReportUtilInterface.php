<?php

namespace Tagcade\Service\Reporting;

use DateTime;
use Tagcade\Exception\Report\InvalidDateException;

interface ReportUtilInterface
{
    /**
     * Get a DateTime object
     * If $date is null, today's date is returned
     * An 6 digit integer should be supplied in the format YYMMDD
     * The PHP date format is 'ymd'
     *
     * @param int|DateTime|null $dateString
     * @param bool $returnTodayIfEmpty
     * @return DateTime
     * @throws InvalidDateException when an incorrect date format is supplied
     */
    public function getDateTime($dateString = null, $returnTodayIfEmpty = false);

    public function getDateRangeMetaFromStr($startDate, $endDate = null);

    public function getDateRangeMeta(DateTime $startDate, DateTime $endDate = null);
}