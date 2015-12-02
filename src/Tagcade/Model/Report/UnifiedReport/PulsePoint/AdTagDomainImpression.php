<?php

namespace Tagcade\Model\Report\UnifiedReport\PulsePoint;

class AdTagDomainImpression implements PulsePointUnifiedReportModelInterface
{
    protected $id;
    protected $publisherId;
    protected $domain;
    protected $totalImps;
    protected $paidImps;
    protected $fillRate;
    protected $domainStatus;
    protected $adTag;
    protected $adTagId;
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
     * @return mixed
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @param mixed $domain
     * @return $this
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;

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
     * @return $this
     */
    public function setTotalImps($totalImps)
    {
        $this->totalImps = $totalImps;

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
     * @return $this
     */
    public function setPaidImps($paidImps)
    {
        $this->paidImps = $paidImps;

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
     * @return $this
     */
    public function setFillRate($fillRate)
    {
        $this->fillRate = $fillRate;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDomainStatus()
    {
        return $this->domainStatus;
    }

    /**
     * @param mixed $domainStatus
     * @return $this
     */
    public function setDomainStatus($domainStatus)
    {
        $this->domainStatus = $domainStatus;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAdTag()
    {
        return $this->adTag;
    }

    /**
     * @param mixed $adTag
     * @return $this
     */
    public function setAdTag($adTag)
    {
        $this->adTag = $adTag;

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
     * @return $this
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

    /**
     * @return mixed
     */
    public function getAdTagId()
    {
        return $this->adTagId;
    }

    /**
     * @param mixed $adTagId
     */
    public function setAdTagId($adTagId)
    {
        $this->adTagId = $adTagId;
    }
}