<?php

namespace Tagcade\Domain\DTO\Report\UnifiedReport;


class AverageValue
{
    /**
     * @var int
     */
    protected $paidImps;
    /**
     * @var int
     */
    protected $paidImpressions;
    /**
     * @var int
     */
    protected $totalImps;
    /**
     * @var int
     */
    protected $allImpressions;

    /**
     * @var float
     */
    protected $cpm;
    /**
     * @var float
     */
    protected $averageCpm;
    /**
     * @var float
     */
    protected $pubPayout;
    /**
     * @var float
     */
    protected $fillRate;
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
    /**
     * @var float
     */
    protected $revenue;
    /**
     * @var int
     */
    protected $backupImpression;
    /**
     * @var float
     */
    protected $avgCpm;
    /**
     * @var float
     */
    protected $averageAvgCpm;
    /**
     * @var float
     */
    protected $averageRevenue;
    /**
     * @var float
     */
    protected $averageBackupImpression;
    /**
     * @var float
     */
    protected $averagePubPayout;

    function __construct()
    {
    }

    /**
     * @return int
     */
    public function getPaidImps()
    {
        return $this->paidImps;
    }

    /**
     * @return int
     */
    public function getTotalImps()
    {
        return $this->totalImps;
    }

    /**
     * @return float
     */
    public function getFillRate()
    {
        return $this->fillRate;
    }

    /**
     * @return float
     */
    public function getAverageFillRate()
    {
        return $this->averageFillRate;
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
    public function getAveragePaidImps()
    {
        return $this->averagePaidImps;
    }

    /**
     * @return float
     */
    public function getRevenue()
    {
        return $this->revenue;
    }

    /**
     * @return int
     */
    public function getBackupImpression()
    {
        return $this->backupImpression;
    }

    /**
     * @return float
     */
    public function getAvgCpm()
    {
        return $this->avgCpm;
    }

    /**
     * @return float
     */
    public function getAverageAvgCpm()
    {
        return $this->averageAvgCpm;
    }

    /**
     * @return float
     */
    public function getAverageRevenue()
    {
        return $this->averageRevenue;
    }

    /**
     * @return float
     */
    public function getAverageBackupImpression()
    {
        return $this->averageBackupImpression;
    }

    /**
     * @return int
     */
    public function getPaidImpressions()
    {
        return $this->paidImpressions;
    }

    /**
     * @return int
     */
    public function getAllImpressions()
    {
        return $this->allImpressions;
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
     * @param int $paidImps
     * @return self
     */
    public function setPaidImps($paidImps)
    {
        $this->paidImps = $paidImps;

        return $this;
    }

    /**
     * @param int $totalImps
     * @return self
     */
    public function setTotalImps($totalImps)
    {
        $this->totalImps = $totalImps;

        return $this;
    }

    /**
     * @param float $fillRate
     * @return self
     */
    public function setFillRate($fillRate)
    {
        $this->fillRate = $fillRate;

        return $this;
    }

    /**
     * @param float $averageFillRate
     * @return self
     */
    public function setAverageFillRate($averageFillRate)
    {
        $this->averageFillRate = $averageFillRate;

        return $this;
    }

    /**
     * @param float $averageTotalImps
     * @return self
     */
    public function setAverageTotalImps($averageTotalImps)
    {
        $this->averageTotalImps = $averageTotalImps;

        return $this;
    }

    /**
     * @param float $averagePaidImps
     * @return self
     */
    public function setAveragePaidImps($averagePaidImps)
    {
        $this->averagePaidImps = $averagePaidImps;

        return $this;
    }

    /**
     * @param float $revenue
     * @return self
     */
    public function setRevenue($revenue)
    {
        $this->revenue = $revenue;

        return $this;
    }

    /**
     * @param int $backupImpression
     * @return self
     */
    public function setBackupImpression($backupImpression)
    {
        $this->backupImpression = $backupImpression;

        return $this;
    }

    /**
     * @param float $avgCpm
     * @return self
     */
    public function setAvgCpm($avgCpm)
    {
        $this->avgCpm = $avgCpm;

        return $this;
    }

    /**
     * @param float $averageAvgCpm
     * @return self
     */
    public function setAverageAvgCpm($averageAvgCpm)
    {
        $this->averageAvgCpm = $averageAvgCpm;

        return $this;
    }

    /**
     * @param float $averageRevenue
     * @return self
     */
    public function setAverageRevenue($averageRevenue)
    {
        $this->averageRevenue = $averageRevenue;

        return $this;
    }

    /**
     * @param float $averageBackupImpression
     * @return self
     */
    public function setAverageBackupImpression($averageBackupImpression)
    {
        $this->averageBackupImpression = $averageBackupImpression;

        return $this;
    }

    /**
     * @param int $paidImpressions
     * @return self
     */
    public function setPaidImpressions($paidImpressions)
    {
        $this->paidImpressions = $paidImpressions;

        return $this;
    }

    /**
     * @param int $allImpressions
     * @return self
     */
    public function setAllImpressions($allImpressions)
    {
        $this->allImpressions = $allImpressions;

        return $this;
    }

    /**
     * @param float $cpm
     * @return self
     */
    public function setCpm($cpm)
    {
        $this->cpm = $cpm;

        return $this;
    }

    /**
     * @param float $pubPayout
     * @return self
     */
    public function setPubPayout($pubPayout)
    {
        $this->pubPayout = $pubPayout;

        return $this;
    }

    /**
     * @param float $averagePubPayout
     * @return $this
     */
    public function setAveragePubPayout($averagePubPayout)
    {
        $this->averagePubPayout = $averagePubPayout;

        return $this;
    }

    /**
     * @return float
     */
    public function getAverageCpm()
    {
        return $this->averageCpm;

    }

    /**
     * @param float $averageCpm
     * @return $this
     */
    public function setAverageCpm($averageCpm)
    {
        $this->averageCpm = $averageCpm;

        return $this;
    }
}