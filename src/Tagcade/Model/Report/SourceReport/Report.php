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
     * @var string
     */
    protected $site;

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