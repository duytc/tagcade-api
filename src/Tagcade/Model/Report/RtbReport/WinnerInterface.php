<?php

namespace Tagcade\Model\Report\RtbReport;

use Tagcade\Model\ModelInterface;

/**
 * Interface WinnerInterface
 * @package Tagcade\Model\Report\RtbReport
 */
interface WinnerInterface extends ModelInterface
{
    /**
     * @param $id
     * @return mixed
     */
    public function setId($id);

    /**
     * Set ImpId
     *
     * @param integer $impId
     *
     * @return self
     */
    public function setImpId($impId);

    /**
     * Get ImpId
     *
     * @return integer
     */
    public function getImpId();

    /**
     * Set tagId
     *
     * @param integer $tagId
     *
     * @return self
     */
    public function setTagId($tagId);

    /**
     * Get tagId
     *
     * @return integer
     */
    public function getTagId();

    /**
     * Set price
     *
     * @param float $price
     *
     * @return self
     */
    public function setPrice($price);

    /**
     * Get price
     *
     * @return float
     */
    public function getPrice();

    /**
     * Set adm
     *
     * @param string $adm
     *
     * @return self
     */
    public function setAdm($adm);

    /**
     * Get adm
     *
     * @return string
     */
    public function getAdm();


    /**
     * @return \DateTime
     */
    public function getDate();

    /**
     * @param \DateTime $date
     * @return self
     */
    public function setDate($date);

    /**
     * @return int
     */
    public function getDspId();

    /**
     * @param $dspId int
     * @return self
     */
    public function setDspId($dspId);

    /**
     * @return boolean
     */
    public function isVerified();

    /**
     * @param boolean $verified
     * @return self
     */
    public function setVerified($verified);

    /**
     * @return String
     */
    public function getBidRequestId();

    /**
     * @param String $bidRequestId
     * @return self
     */
    public function setBidRequestId($bidRequestId);
}

