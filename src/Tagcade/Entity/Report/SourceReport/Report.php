<?php

namespace Tagcade\Entity\Report\SourceReport;

use \DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="source_reports", uniqueConstraints={@ORM\UniqueConstraint(name="unique_report_idx", columns={"date", "site"})})
 */
class Report
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     **/
    protected $id;
    /**
     * @ORM\Column(type="date")
     */
    protected $date;
    /**
     * @ORM\Column(type="string")
     */
    protected $site;
    /**
     * @ORM\OneToMany(targetEntity="Record", mappedBy="sourceReport", cascade={"persist", "remove"})
     */
    protected $records;

    public function __construct()
    {
        $this->records = new ArrayCollection();
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getRecords()
    {
        return $this->records;
    }

    public function addRecord(Record $item)
    {
        $item->setSourceReport($this);
        $this->records[] = $item;
    }

    /**
     * @return string
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * @param string $site
     */
    public function setSite($site)
    {
        $this->site = $site;
    }
}