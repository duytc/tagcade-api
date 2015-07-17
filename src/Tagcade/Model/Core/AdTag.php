<?php

namespace Tagcade\Model\Core;

class AdTag implements AdTagInterface
{
    protected $id;

    /**
     * @var BaseAdSlotInterface
     */
    protected $adSlot;

    /**
     * @var AdNetworkInterface
     */
    protected $adNetwork;
    protected $name;
    protected $html;
    protected $position;
    protected $active;
    protected $frequencyCap;

    /** int - for rotation display AdTags */
    protected $rotation;
    /** int - type of AdTags*/
    protected $adType = 0;
    /** array - json_array, descriptor of AdTag*/
    protected $descriptor;

    /**
     * @param string $name
     * @param string $html
     * @param AdNetworkInterface|null $adNetwork
     */
    public function __construct($name, $html, AdNetworkInterface $adNetwork = null)
    {
        $this->name = $name;
        $this->html = $html;

        if ($adNetwork) {
            $this->setAdNetwork($adNetwork);
        }
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @inheritdoc
     */
    public function getAdSlot()
    {
        return $this->adSlot;
    }

    /**
     * @inheritdoc
     */
    public function getAdSlotId()
    {
        if (!$this->adSlot) {
            return null;
        }

        return $this->adSlot->getId();
    }

    public function setAdSlot(ReportableAdSlotInterface $adSlot)
    {
        $this->adSlot = $adSlot;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAdNetwork()
    {
        return $this->adNetwork;
    }

    /**
     * @inheritdoc
     */
    public function getAdNetworkId()
    {
        if (!$this->adNetwork) {
            return null;
        }

        return $this->adNetwork->getId();
    }

    public function setAdNetwork(AdNetworkInterface $adNetwork)
    {
        $this->adNetwork = $adNetwork;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getHtml()
    {
        return $this->html;
    }

    /**
     * @inheritdoc
     */
    public function setHtml($html)
    {
        $this->html = $html;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @inheritdoc
     */
    public function setPosition($position)
    {
        $this->position = $position;
        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @inheritdoc
     */
    public function setActive($boolean)
    {
        $this->active = (Boolean)$boolean;
        return $this;
    }

    /**
     * @param int|null $frequencyCap
     * @return $this
     */
    public function setFrequencyCap($frequencyCap)
    {
        $this->frequencyCap = $frequencyCap;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getFrequencyCap()
    {
        return $this->frequencyCap;
    }

    /**
     * @inheritdoc
     */
    public function setRotation($rotation)
    {
        $this->rotation = $rotation;
    }

    /**
     * @inheritdoc
     */
    public function getRotation()
    {
        return $this->rotation;
    }

    /**
     * @inheritdoc
     */
    public function getAdType()
    {
        return $this->adType;
    }

    /**
     * @inheritdoc
     */
    public function setAdType($adType)
    {
        $this->adType = $adType;
    }

    /**
     * @inheritdoc
     */
    public function getDescriptor()
    {
        return $this->descriptor;
    }

    /**
     * @inheritdoc
     */
    public function setDescriptor($descriptor)
    {
        $this->descriptor = $descriptor;
    }
}