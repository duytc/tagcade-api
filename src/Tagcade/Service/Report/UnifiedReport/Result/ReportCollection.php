<?php

namespace Tagcade\Service\Report\UnifiedReport\Result;

use ArrayIterator;
use DateTime;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Tagcade\Domain\DTO\Report\UnifiedReport\AverageValue;
use Tagcade\Model\Report\PerformanceReport\Display\ReportDataInterface;
use Tagcade\Model\Report\UnifiedReport\ReportType\ReportTypeInterface;

class ReportCollection implements ReportResultInterface
{
    protected $reportType;
    protected $startDate;
    protected $endDate;
    /**
     * @var array
     */
    protected $reports;
    /**
     * @var int
     */
    protected $totalRecord;
    protected $name;

    // as total value
    /**
     * @var int
     */
    protected $paidImps;
    protected $paidImpressions;
    /**
     * @var int
     */
    protected $totalImps;
    protected $allImpressions;

    // as weighted value
    /**
     * @var float
     */
    protected $fillRate;

    // as average value
    /**
     * @var float
     */
    protected $averageFillRate;
    /**
     * @var float
     */
    protected $averageTotalImps;
    /**
     * @var float
     */
    protected $averagePaidImps;
    protected $averageCpm;
    protected $cpm;
    protected $pubPayout;
    protected $averagePubPayout;


    /**
     * @param ReportTypeInterface|ReportTypeInterface[] $reportType
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param array $reports
     * @param int $totalRecord
     * @param string $name
     * @param AverageValue $avg
     */
    public function __construct($reportType, DateTime $startDate, DateTime $endDate, $reports, $totalRecord, $name = null, AverageValue $avg)
    {
        $this->reportType = $reportType;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->name = $name;
        $this->reports = $reports;
        $this->totalRecord = intval($totalRecord);
        $this->paidImps = intval($avg->getPaidImps());
        $this->totalImps = intval($avg->getTotalImps());
        $this->fillRate = floatval($avg->getFillRate());
        $this->averageFillRate = $avg->getAverageFillRate();
        $this->averageTotalImps = $avg->getAverageTotalImps();
        $this->averagePaidImps = $avg->getAveragePaidImps();
        $this->paidImpressions = intval($avg->getPaidImpressions());
        $this->allImpressions = intval($avg->getAllImpressions());
        $this->pubPayout = floatval($avg->getPubPayout());
        $this->averagePubPayout = floatval($avg->getAveragePubPayout());
        $this->cpm = floatval($avg->getCpm());
        $this->averageCpm = floatval($avg->getAvgCpm());
    }

    /**
     * @return ReportTypeInterface|ReportTypeInterface[]
     */
    public function getReportType()
    {
        return $this->reportType;
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
     * @return ReportDataInterface[]
     */
    public function getReports()
    {
        return $this->reports;
    }

    public function getIterator()
    {
        return new ArrayIterator($this->getReports());
    }

    /**
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getTotalRecord()
    {
        $this->totalRecord;
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
    public function getFillRate()
    {
        return $this->fillRate;
    }

    /**
     * @return mixed
     */
    public function getAverageFillRate()
    {
        return $this->averageFillRate;
    }

    /**
     * @return mixed
     */
    public function getAverageTotalImps()
    {
        return $this->averageTotalImps;
    }

    /**
     * @return mixed
     */
    public function getAveragePaidImps()
    {
        return $this->averagePaidImps;
    }

    /**
     * @return mixed
     */
    public function getPaidImpressions()
    {
        return $this->paidImpressions;
    }

    /**
     * @return mixed
     */
    public function getAllImpressions()
    {
        return $this->allImpressions;
    }

    /**
     * @return mixed
     */
    public function getCpm()
    {
        return $this->cpm;
    }

    /**
     * @return mixed
     */
    public function getPubPayout()
    {
        return $this->pubPayout;
    }

    /**
     * @return mixed
     */
    public function getAveragePubPayout()
    {
        return $this->averagePubPayout;
    }

    /**
     * @return mixed
     */
    public function getAverageCpm()
    {
        return $this->averageCpm;
    }
}