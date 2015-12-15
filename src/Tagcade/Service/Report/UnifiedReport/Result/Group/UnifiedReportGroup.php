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
    protected $cpm;
    protected $pubPayout;

    // as weighted value
    protected $fillRate;

    // as average value
    protected $averagePubPayout;
    protected $averageCpm;
    protected $averageFillRate;
    protected $averageTotalImps;
    protected $averagePaidImps;

    public function __construct($reportType, \DateTime $startDate, \DateTime $endDate, $reports, $totalRecord, $name, AverageValue $avg)
    {
        $this->reportType = $reportType;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->reports = $reports;
        $this->totalRecord = $totalRecord;
        $this->name = $name;

        // total report
        $this->paidImps = round($avg->getPaidImps(), 0);
        $this->totalImps = round($avg->getTotalImps(), 0);
        $this->cpm = floatval($avg->getCpm());
        $this->pubPayout = floatval($avg->getPubPayout());

        // weighted report
        $this->fillRate = round($avg->getFillRate(), 4);

        // average report
        $this->averageCpm = floatval($avg->getAverageCpm());
        $this->averagePubPayout = floatval($avg->getAveragePubPayout());
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

    /**
     * @return float
     */
    public function getCpm()
    {
        return $this->cpm;
    }

    /**
     * @return float
     */
    public function getPubPayout()
    {
        return $this->pubPayout;
    }

    /**
     * @return float
     */
    public function getAveragePubPayout()
    {
        return $this->averagePubPayout;
    }

    /**
     * @return float
     */
    public function getAverageCpm()
    {
        return $this->averageCpm;
    }

    /**
     * @return float
     */
    public function getFillRate()
    {
        return $this->fillRate;
    }

    /**
     * @return mixed
     */
    public function getTotalRecord()
    {
        return $this->totalRecord;
    }
}