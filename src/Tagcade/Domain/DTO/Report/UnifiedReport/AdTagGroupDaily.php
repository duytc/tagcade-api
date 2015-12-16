<?php

namespace Tagcade\Domain\DTO\Report\UnifiedReport;


use Tagcade\Model\Report\UnifiedReport\PulsePoint\PulsePointUnifiedReportRevenueInterface;

class AdTagGroupDaily implements PulsePointUnifiedReportRevenueInterface
{
    protected $id;
    protected $publisherId;
    protected $adTagGroup;
    protected $revenue;
    protected $fillRate;
    protected $paidImps;
    protected $backupImpression;
    protected $totalImps;
    protected $avgCpm;
    protected $date;

    function __construct()
    {
        // TODO: Implement __construct() method.
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAdTagGroup()
    {
        return $this->adTagGroup;
    }

    /**
     * @param mixed $adTagGroup
     * @return self
     */
    public function setAdTagGroup($adTagGroup)
    {
        $this->adTagGroup = $adTagGroup;

        return $this;
    }

    /**
     * @return float
     */
    public function getRevenue()
    {
        return $this->revenue;
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
     * @return float
     */
    public function getFillRate()
    {
        return $this->fillRate;
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
     * @return float
     */
    public function getPaidImps()
    {
        return $this->paidImps;
    }

    /**
     * @param float $paidImps
     * @return self
     */
    public function setPaidImps($paidImps)
    {
        $this->paidImps = $paidImps;

        return $this;
    }

    /**
     * @return float
     */
    public function getTotalImps()
    {
        return $this->totalImps;
    }

    /**
     * @param float $totalImps
     * @return self
     */
    public function setTotalImps($totalImps)
    {
        $this->totalImps = $totalImps;

        return $this;
    }

    /**
     * @return float
     */
    public function getBackupImpression()
    {
        return $this->backupImpression;
    }

    /**
     * @param float $backupImpression
     * @return self
     */
    public function setBackupImpression($backupImpression)
    {
        $this->backupImpression = $backupImpression;

        return $this;
    }

    /**
     * @return float
     */
    public function getAvgCpm()
    {
        return $this->avgCpm;
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
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $date
     * @return self
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPublisherId()
    {
        return $this->publisherId;
    }

    /**
     * @param mixed $publisherId
     * @return $this
     */
    public function setPublisherId($publisherId)
    {
        $this->publisherId = $publisherId;

        return $this;
    }
}