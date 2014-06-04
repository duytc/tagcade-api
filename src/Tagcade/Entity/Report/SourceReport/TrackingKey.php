<?php

namespace Tagcade\Entity\Report\SourceReport;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="tracking_keys")
 */
class TrackingKey
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     **/
    protected $id;
    /**
     * @ORM\ManyToOne(targetEntity="TrackingTerm", cascade={"persist"})
     */
    protected $trackingTerm;
    /**
     * @ORM\Column(type="string")
     * @var string
     */
    protected $value;

    /**
     * @return TrackingTerm
     */
    public function getTrackingTerm()
    {
        return $this->trackingTerm;
    }

    /**
     * @param TrackingTerm $trackingTerm
     */
    public function setTrackingTerm(TrackingTerm $trackingTerm)
    {
        $this->trackingTerm = $trackingTerm;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }
}