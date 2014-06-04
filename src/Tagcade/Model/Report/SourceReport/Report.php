<?php

namespace Tagcade\Model\Report\SourceReport;

use \DateTime;

class Report
{
    protected $id;
    /**
     * @var DateTime
     */
    protected $date;

    /**
     * @var string
     */
    protected $site;

    /**
     * @var array
     */
    protected $records;

    /**
     * @param DateTime $date
     * @param string $site
     */
    public function __construct(DateTime $date, $site)
    {
        $this->date = $date;
        $this->site = $site;
        $this->records = [];
    }

    /**
     * @return DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param DateTime $date
     * @return $this
     */
    public function setDate(DateTime $date)
    {
        $this->date = $date;

        return $this;
    }

    public function getRecords()
    {
        return $this->records;
    }

    /**
     * @param Record $item
     * @return $this
     */
    public function addRecord(Record $item)
    {
        $item->setSourceReport($this);
        $this->records[] = $item;

        return $this;
    }

    public function getSite()
    {
        return $this->site;
    }

    /**
     * @param string $site
     * @return $this
     */
    public function setSite($site)
    {
        $this->site = $site;

        return $this;
    }
}