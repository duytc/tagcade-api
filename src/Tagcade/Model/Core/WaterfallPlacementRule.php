<?php


namespace Tagcade\Model\Core;


class WaterfallPlacementRule implements WaterfallPlacementRuleInterface
{
    const PLACEMENT_PROFIT_TYPE_FIX_MARGIN = 1;
    const PLACEMENT_PROFIT_TYPE_PERCENTAGE_MARGIN = 2;
    const PLACEMENT_PROFIT_TYPE_MANUAL = 3;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var int
     */
    protected $profitType;

    /**
     * @var float
     */
    protected $profitValue;

    /**
     * @var array
     */
    protected $publishers;

    /**
     * @var integer
     */
    protected $position;

    /**
     * @var integer
     */
    protected $rotationWeight;

    /**
     * @var integer
     */
    protected $priority;

    /**
     * @var array
     */
    protected $waterfalls;

    /**
     * @var boolean
     */
    protected $active;

    /**
     * @var boolean
     */
    protected $shiftDown;

    protected $deletedAt;

    /**
     * @var LibraryVideoDemandAdTagInterface
     */
    protected $libraryVideoDemandAdTag;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getProfitType()
    {
        return $this->profitType;
    }

    /**
     * @param int $profitType
     * @return self
     */
    public function setProfitType($profitType)
    {
        $this->profitType = $profitType;
        return $this;
    }

    /**
     * @return float
     */
    public function getProfitValue()
    {
        return $this->profitValue;
    }

    /**
     * @param float $profitValue
     * @return self
     */
    public function setProfitValue($profitValue)
    {
        $this->profitValue = $profitValue;
        return $this;
    }

    /**
     * @return array
     */
    public function getPublishers()
    {
        return $this->publishers;
    }

    /**
     * @param array $publishers
     * @return self
     */
    public function setPublishers($publishers)
    {
        $this->publishers = $publishers;
        return $this;
    }

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param int $position
     * @return self
     */
    public function setPosition($position)
    {
        $this->position = $position;
        return $this;
    }

    /**
     * @return int
     */
    public function getRotationWeight()
    {
        return $this->rotationWeight;
    }

    /**
     * @param int $rotationWeight
     * @return self
     */
    public function setRotationWeight($rotationWeight)
    {
        $this->rotationWeight = $rotationWeight;
        return $this;
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @param int $priority
     * @return self
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
        return $this;
    }

    /**
     * @return array
     */
    public function getWaterfalls()
    {
        return $this->waterfalls;
    }

    /**
     * @param array $waterfalls
     * @return self
     */
    public function setWaterfalls($waterfalls)
    {
        $this->waterfalls = $waterfalls;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param boolean $active
     * @return self
     */
    public function setActive($active)
    {
        $this->active = $active;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isShiftDown()
    {
        return $this->shiftDown;
    }

    /**
     * @param boolean $shiftDown
     * @return self
     */
    public function setShiftDown($shiftDown)
    {
        $this->shiftDown = $shiftDown;
        return $this;
    }


    /**
     * @return mixed
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }


    /**
     * @return LibraryVideoDemandAdTagInterface
     */
    public function getLibraryVideoDemandAdTag()
    {
        return $this->libraryVideoDemandAdTag;
    }

    /**
     * @param LibraryVideoDemandAdTagInterface $libraryVideoDemandAdTag
     * @return self
     */
    public function setLibraryVideoDemandAdTag($libraryVideoDemandAdTag)
    {
        $this->libraryVideoDemandAdTag = $libraryVideoDemandAdTag;
        return $this;
    }

    public function __toString()
    {
        return $this->id . '-' . $this->getProfitValue();
    }
}