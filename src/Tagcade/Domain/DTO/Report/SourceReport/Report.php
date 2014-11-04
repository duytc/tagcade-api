<?php

namespace Tagcade\Domain\DTO\Report\SourceReport;

use DateTime;

class Report
{
    private $id;
    private $date;
    private $siteId;
    private $records;

    /**
     * @param int $id
     * @param DateTime $date
     * @param int $siteId
     * @param array $records
     */
    public function __construct($id, DateTime $date, $siteId, array $records)
    {
        $this->id = $id;
        $this->date = $date;
        $this->siteId = $siteId;
        $this->records = $records;
    }

    public function getId()
    {
        return $this->id;
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