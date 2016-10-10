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
     * @var int
     */
    protected $profitValue;

    /**
     * @var array
     */
    protected $publishers;

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
     * @return int
     */
    public function getProfitValue()
    {
        return $this->profitValue;
    }

    /**
     * @param int $profitValue
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