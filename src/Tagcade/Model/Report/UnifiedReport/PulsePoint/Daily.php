<?php

namespace Tagcade\Model\Report\UnifiedReport\PulsePoint;

class Daily implements PulsePointUnifiedReportRevenueInterface
{
    protected $id;
    protected $publisherId;
    protected $date;
    protected $size;
    protected $revenue;
    protected $fillRate;
    protected $paidImps;
    protected $backupImpression;
    protected $totalImps;
    protected $avgCpm;

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
     * @return mixed
     */
    public function getRevenue()
    {
        return $this->revenue;
    }

    /**
     * @param mixed $revenue
     * @return self
     */
    public function setRevenue($revenue)
    {
        $this->revenue = $revenue;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getFillRate()
    {
        return $this->fillRate;
    }

    /**
     * @param mixed $fillRate
     * @return self
     */
    public function setFillRate($fillRate)
    {
        $this->fillRate = $fillRate;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPaidImps()
    {
        return $this->paidImps;
    }

    /**
     * @param mixed $paidImps
     * @return self
     */
    public function setPaidImps($paidImps)
    {
        $this->paidImps = $paidImps;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTotalImps()
    {
        return $this->totalImps;
    }

    /**
     * @param mixed $totalImps
     * @return self
     */
    public function setTotalImps($totalImps)
    {
        $this->totalImps = $totalImps;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getBackupImpression()
    {
        return $this->backupImpression;
    }

    /**
     * @param mixed $backupImpression
     * @return self
     */
    public function setBackupImpression($backupImpression)
    {
        $this->backupImpression = $backupImpression;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAvgCpm()
    {
        return $this->avgCpm;
    }

    /**
     * @param mixed $avgCpm
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
     */
    public function setPublisherId($publisherId)
    {
        $this->publisherId = $publisherId;
    }

    /**
     * @return mixed
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param mixed $size
     */
    public function setSize($size)
    {
        $this->size = $size;
    }
}