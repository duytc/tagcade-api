<?php

namespace Tagcade\Model\Report\SourceReport;

class TrackingKey
{
    protected $id;

    /**
     * @var TrackingTerm
     */
    protected $trackingTerm;

    /**
     * @var string
     */
    protected $value;

    public function getTrackingTerm()
    {
        return $this->trackingTerm;
    }

    /**
     * @param TrackingTerm $trackingTerm
     * @return $this
     */
    public function setTrackingTerm(TrackingTerm $trackingTerm)
    {
        $this->trackingTerm = $trackingTerm;

        return $this;
    }

    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }
}