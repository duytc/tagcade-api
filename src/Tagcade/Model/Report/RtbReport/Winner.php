<?php

namespace Tagcade\Model\Report\RtbReport;


/**
 * Class Winner
 * @package Tagcade\Model\Report\RtbReport
 */
class Winner implements WinnerInterface
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var String
     */
    protected $bidRequestId;

    /**
     * @var String
     */

    protected $impId;
    /**
     * @var integer
     */
    protected $tagId;

    /**
     * @var float
     */
    protected $price;

    /**
     * @var string
     */
    protected $adm;

    /**
     * @var integer
     */
    protected $dspId;

    /**
     * @var boolean
     */
    protected $verified;

    /**
     * @var \DateTime
     */
    protected $date;

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return String
     */
    public function getBidRequestId()
    {
        return $this->bidRequestId;
    }

    /**
     * @param String $bidRequestId
     * @return self
     */
    public function setBidRequestId($bidRequestId)
    {
        $this->bidRequestId = $bidRequestId;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setImpId($impId)
    {
        $this->impId = $impId;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getImpId()
    {
        return $this->impId;
    }

    /**
     * @inheritdoc
     */
    public function setTagId($tagId)
    {
        $this->tagId = $tagId;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getTagId()
    {
        return $this->tagId;
    }

    /**
     * @inheritdoc
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @inheritdoc
     */
    public function setAdm($adm)
    {
        $this->adm = $adm;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAdm()
    {
        return $this->adm;
    }

    /**
     * @return int
     */
    public function getDspId()
    {
        return $this->dspId;
    }

    /**
     * @param int $dspId
     * @return self
     */
    public function setDspId($dspId)
    {
        $this->dspId = $dspId;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isVerified()
    {
        return $this->verified;
    }

    /**
     * @param boolean $verified
     * @return self
     */
    public function setVerified($verified)
    {
        $this->verified = $verified;

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
     * @return self
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }
}

