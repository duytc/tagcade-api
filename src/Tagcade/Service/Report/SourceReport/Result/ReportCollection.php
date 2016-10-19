<?php


namespace Tagcade\Service\Report\SourceReport\Result;


use ArrayIterator;
use DateTime;
use Tagcade\Model\Report\SourceReport\ReportInterface;

class ReportCollection implements ReportResultInterface
{
    protected $startDate;
    protected $endDate;
    protected $date;
    protected $reports;
    protected $name;

    /**
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param ReportInterface[] $reports
     * @param string $name
     */
    public function __construct(DateTime $startDate, DateTime $endDate, array $reports, $name = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->name = $name;
        $this->reports = $reports;

        if ($startDate == $endDate) {
            $this->date = $startDate;
        }
    }

    /**
     * @return DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @return DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @return ReportInterface[]
     */
    public function getReports()
    {
        return $this->reports;
    }

    public function getIterator()
    {
        return new ArrayIterator($this->reports);
    }

    /**
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    public function getDate()
    {
        return $this->date;
    }
}