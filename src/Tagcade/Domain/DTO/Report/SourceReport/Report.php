<?php

namespace Tagcade\Domain\DTO\Report\SourceReport;

use DateTime;

class Report
{
    private $date;
    private $siteId;
    private $records;

    /**
     * @param DateTime $date
     * @param int $siteId
     * @param array $records
     */
    public function __construct(DateTime $date, $siteId, array $records)
    {
        $this->date = $date;
        $this->siteId = $siteId;
        $this->records = $records;
    }

    /**
     * @return DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return int
     */
    public function getSiteId()
    {
        return $this->siteId;
    }

    /**
     * @return array
     */
    public function getRecords()
    {
        return $this->records;
    }
}