<?php

namespace Tagcade\Model\Report\UnifiedReport\PulsePoint;

class AdTagDomainImpression implements PulsePointUnifiedReportModelInterface
{
    /**
     * @var int
     */
    protected $id;
    /**
     * @var int
     */
    protected $publisherId;
    /**
     * @var string
     */
    protected $domain;
    /**
     * @var int
     */
    protected $totalImps;
    /**
     * @var int
     */
    protected $paidImps;
    /**
     * @var float
     */
    protected $fillRate;
    /**
     * @var string
     */
    protected $domainStatus;
    /**
     * @var string
     */
    protected $adTag;
    /**
     * @var int
     */
    protected $adTagId;

    /**
     * @var \DateTime
     */
    protected $date;

    function __construct()
    {
        // TODO: Implement __construct() method.
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @param string $domain
     * @return $this
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * @return int
     */
    public function getTotalImps()
    {
        return $this->totalImps;
    }

    /**
     * @param int $totalImps
     * @return $this
     */
    public function setTotalImps($totalImps)
    {
        $this->totalImps = $totalImps;

        return $this;
    }

    /**
     * @return int
     */
    public function getPaidImps()
    {
        return $this->paidImps;
    }

    /**
     * @param int $paidImps
     * @return $this
     */
    public function setPaidImps($paidImps)
    {
        $this->paidImps = $paidImps;

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
     * @return $this
     */
    public function setFillRate($fillRate)
    {
        $this->fillRate = $fillRate;

        return $this;
    }

    /**
     * @return string
     */
    public function getDomainStatus()
    {
        return $this->domainStatus;
    }

    /**
     * @param string $domainStatus
     * @return $this
     */
    public function setDomainStatus($domainStatus)
    {
        $this->domainStatus = $domainStatus;

        return $this;
    }

    /**
     * @return string
     */
    public function getAdTag()
    {
        return $this->adTag;
    }

    /**
     * @param string $adTag
     * @return $this
     */
    public function setAdTag($adTag)
    {
        $this->adTag = $adTag;

        return $this;
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
     * @return $this
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return int
     */
    public function getPublisherId()
    {
        return $this->publisherId;
    }

    /**
     * @param int $publisherId
     * @return $this
     */
    public function setPublisherId($publisherId)
    {
        $this->publisherId = $publisherId;

        return $this;
    }

    /**
     * @return int
     */
    public function getAdTagId()
    {
        return $this->adTagId;
    }

    /**
     * @param int $adTagId
     * @return $this
     */
    public function setAdTagId($adTagId)
    {
        $this->adTagId = $adTagId;

        return $this;
    }
}