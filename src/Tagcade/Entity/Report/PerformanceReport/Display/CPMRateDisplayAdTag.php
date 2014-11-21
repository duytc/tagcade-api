<?php

namespace Tagcade\Entity\Report\PerformanceReport\Display;

use Tagcade\Model\Core\AdTagInterface;

class CPMRateDisplayAdTag
{

    protected $id;
    /**
     * @var AdTagInterface
     */
    protected $adTag;

    protected $date;
    protected $rate;

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
     * @return AdTagInterface
     */
    public function getAdTag()
    {
        return $this->adTag;
    }

    /**
     * @param AdTagInterface $adTag
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
    public function setDate(\DateTime $date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return float
     */
    public function getRate()
    {
        return $this->rate;
    }

    /**
     * @param mixed $rate
     * @return $this
     */
    public function setRate($rate)
    {
        $this->rate = $rate;

        return $this;
    }
}