<?php

namespace Tagcade\Service\Report\UnifiedReport\Result\Group;

use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Tagcade\Domain\DTO\Report\UnifiedReport\AverageValue;

class UnifiedReportGroup
{
    protected $reportType;
    protected $reports;
    protected $totalRecord;
    protected $name;
    protected $startDate;
    protected $endDate;

    // as total value
    /**
     * @var float
     */
    protected $paidImps;
    protected $totalImps;

    // as weighted value
    protected $fillRate;

    // as average value
    protected $averageFillRate;
    protected $averageTotalImps;
    protected $averagePaidImps;

    public function __construct($reportType, \DateTime $startDate, \DateTime $endDate, SlidingPagination $pagination, $name, AverageValue $avg)
    {
        $this->reportType = $reportType;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->reports = $pagination->getItems();
        $this->totalRecord = $pagination->getTotalItemCount();
        $this->name = $name;

        // total report
        $this->paidImps = round($avg->getPaidImps(), 0);
        $this->totalImps = round($avg->getTotalImps(), 0);

        // weighted report
        $this->fillRate = round($avg->getFillRate(), 4);

        // average report
        $this->averageFillRate = round($avg->getAverageFillRate(), 4);
        $this->averagePaidImps = round($avg->getAveragePaidImps(), 4);
        $this->averageTotalImps = round($avg->getAverageTotalImps(), 4);
    }

    /**
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getReportType()
    {
        return $this->reportType;
    }

    /**
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @return array
     */
    public function getReports()
    {
        return $this->reports;
    }

    /**
     * @return mixed
     */
    public function getPaidImps()
    {
        return $this->paidImps;
    }

    /**
     * @return mixed
     */
    public function getTotalImps()
    {
        return $this->totalImps;
    }

    /**
     * @return mixed
     */
    public function getAverageFillRate()
    {
        return $this->averageFillRate;
    }

    /**
     * @return float
     */
    public function getAveragePaidImps()
    {
        return $this->averagePaidImps;
    }

    /**
     * @return float
     */
    public function getAverageTotalImps()
    {
        return $this->averageTotalImps;
    }
}