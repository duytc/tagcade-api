<?php

namespace Tagcade\Domain\DTO\Report\UnifiedReport;


use Tagcade\Model\Report\UnifiedReport\PulsePoint\PulsePointUnifiedReportModelInterface;

class AdTagCountry implements PulsePointUnifiedReportModelInterface
{
    protected $id;
    protected $publisherId;
    protected $tagId;
    protected $adTagName;
    protected $adTagGroupId;
    protected $adTagGroupName;
    protected $country;
    protected $countryName;
    protected $paidImpressions;
    protected $allImpressions;
    protected $pubPayout;
    protected $fillRate;
    protected $cpm;

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
    public function getAdTagGroupId()
    {
        return $this->adTagGroupId;
    }

    /**
     * @param mixed $adTagGroupId
     * @return $this
     */
    public function setAdTagGroupId($adTagGroupId)
    {
        $this->adTagGroupId = $adTagGroupId;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAdTagGroupName()
    {
        return $this->adTagGroupName;
    }

    /**
     * @param mixed $adTagGroupName
     * @return $this
     */
    public function setAdTagGroupName($adTagGroupName)
    {
        $this->adTagGroupName = $adTagGroupName;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAdTagName()
    {
        return $this->adTagName;
    }

    /**
     * @param mixed $adTagName
     * @return $this
     */
    public function setAdTagName($adTagName)
    {
        $this->adTagName = $adTagName;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTotalImps()
    {
        return $this->allImpressions;
    }

    /**
     * @param mixed $allImpressions
     * @return $this
     */
    public function setTotalImps($allImpressions)
    {
        $this->allImpressions = $allImpressions;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param mixed $country
     * @return $this
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCountryName()
    {
        return $this->countryName;
    }

    /**
     * @param mixed $countryName
     * @return $this
     */
    public function setCountryName($countryName)
    {
        $this->countryName = $countryName;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCpm()
    {
        return $this->cpm;
    }

    /**
     * @param mixed $cpm
     * @return $this
     */
    public function setCpm($cpm)
    {
        $this->cpm = $cpm;

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
    public function getPaidImps()
    {
        return $this->paidImpressions;
    }

    /**
     * @param mixed $paidImpressions
     * @return $this
     */
    public function setPaidImps($paidImpressions)
    {
        $this->paidImpressions = $paidImpressions;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPubPayout()
    {
        return $this->pubPayout;
    }

    /**
     * @param mixed $pubPayout
     * @return $this
     */
    public function setPubPayout($pubPayout)
    {
        $this->pubPayout = $pubPayout;

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
    public function getTagId()
    {
        return $this->tagId;
    }

    /**
     * @param mixed $tagId
     * @return $this
     */
    public function setTagId($tagId)
    {
        $this->tagId = $tagId;

        return $this;
    }
}