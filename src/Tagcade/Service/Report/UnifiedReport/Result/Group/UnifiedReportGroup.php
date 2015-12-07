<?php

namespace Tagcade\Service\Report\UnifiedReport\Result\Group;

use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;

class UnifiedReportGroup
{
    protected $reportType;
    protected $reports;
    protected $totalRecord;
    protected $name;
    protected $startDate;
    protected $endDate;

    // as total value
    protected $paidImps;
    protected $totalImps;

    // as weighted value
    protected $fillRate;

    // as average value
    protected $averageFillRate;
    protected $averageTotalImps;
    protected $averagePaidImps;

    public function __construct($reportType, \DateTime $startDate, \DateTime $endDate, SlidingPagination $pagination, $name, $paidImps, $totalImps, $fillRate,
                                $averageFillRate, $averagePaidImps, $averageTotalImps)
    {
        $this->reportType = $reportType;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->reports = $pagination->getItems();
        $this->totalRecord = $pagination->getTotalItemCount();
        $this->name = $name;

        // total report
        $this->paidImps = $paidImps;
        $this->totalImps = $totalImps;

        // weighted report
        $this->fillRate = round($fillRate, 4);

        // average report
        $this->averageFillRate = round($averageFillRate, 4);
        $this->averagePaidImps = round($averagePaidImps, 4);
        $this->averageTotalImps = round($averageTotalImps, 4);
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