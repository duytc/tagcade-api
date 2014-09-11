<?php

namespace Tagcade\Model\Core;

class AdTag implements AdTagInterface
{
    protected $id;

    /**
     * @var AdSlotInterface
     */
    protected $adSlot;

    /**
     * @var AdNetworkInterface
     */
    protected $adNetwork;
    protected $name;
    protected $html;
    protected $position;

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

    public function setAdSlot(AdSlotInterface $adSlot)
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
}