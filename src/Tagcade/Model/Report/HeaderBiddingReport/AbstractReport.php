<?php

namespace Tagcade\Model\Report\HeaderBiddingReport;

use DateTime;
use Tagcade\Model\Report\CalculateRatiosTrait;

abstract class AbstractReport implements ReportInterface
{
    use CalculateRatiosTrait;

    protected $id;
    protected $date;
    protected $name;
    protected $requests;
    protected $billedRate;
    protected $billedAmount;

    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @inheritdoc
     */
    public function setDate(DateTime $date = null)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRequests()
    {
        return $this->requests;
    }

    /**
     * @param mixed $requests
     * @return self
     */
    public function setRequests($requests)
    {
        $this->requests = (int) $requests;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBilledRate()
    {
        return $this->billedRate;
    }

    /**
     * @param mixed $billedRate
     * @return self
     */
    public function setBilledRate($billedRate)
    {
        $this->billedRate = $billedRate;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBilledAmount()
    {
        return $this->billedAmount;
    }

    /**
     * @param mixed $billedAmount
     * @return self
     */
    public function setBilledAmount($billedAmount)
    {
        $this->billedAmount = $billedAmount;
        return $this;
    }

    public function setCalculatedFields()
    {
        if ($this->getName() === null) {
            $this->setDefaultName();
        }
    }

    protected function setDefaultName()
    {
        // currently we do nothing here
    }
}