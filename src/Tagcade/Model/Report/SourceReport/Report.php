<?php

namespace Tagcade\Model\Report\SourceReport;

use Doctrine\Common\Collections\ArrayCollection;
use \DateTime;

class Report
{
    protected $id;
    /**
     * @var DateTime
     */
    protected $date;

    /**
     * @var integer
     * this is not a foreign key at the moment, just an integer
     */
    protected $siteId;

    /**
     * @var array
     */
    protected $records;

    public function __construct()
    {
        $this->records = new ArrayCollection();
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

    public function getSiteId()
    {
        return $this->siteId;
    }

    /**
     * @param int $siteId
     * @return $this
     */
    public function setSiteId($siteId)
    {
        $this->siteId = $siteId;

        return $this;
    }
}