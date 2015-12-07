<?php

namespace Tagcade\Model\Report\UnifiedReport\PulsePoint;

class DomainImpression implements PulsePointUnifiedReportModelInterface
{
    protected $id;
    protected $publisherId;
    protected $domain;
    protected $totalImps;
    protected $paidImps;
    protected $fillRate;
    protected $domainStatus;
    protected $adTags;
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
     * @return self
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
    public function getDomainStatus()
    {
        return $this->domainStatus;
    }

    /**
     * @param mixed $domainStatus
     * @return self
     */
    public function setDomainStatus($domainStatus)
    {
        $this->domainStatus = $domainStatus;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAdTags()
    {
        return $this->adTags;
    }

    /**
     * @return mixed
     */
    public function getAdTagsJson()
    {
        return is_array($this->adTags) ? json_encode($this->adTags) : null;
    }

    /**
     * @param mixed $adTags
     * @return self
     */
    public function setAdTags($adTags)
    {
        $this->adTags = $adTags;

        return $this;
    }

    /**
     * @param $adTag
     */
    public function addAdTag($adTag)
    {
        if ($this->adTags === null) {
            $this->adTags = [];
        }

        $this->adTags[] = $adTag;
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


}