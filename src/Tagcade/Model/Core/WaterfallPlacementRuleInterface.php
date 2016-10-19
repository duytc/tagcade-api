<?php


namespace Tagcade\Model\Core;


use Tagcade\Model\ModelInterface;

interface WaterfallPlacementRuleInterface extends ModelInterface
{
    /**
     * @param int $id
     * @return self
     */
    public function setId($id);

    /**
     * @return int
     */
    public function getProfitType();

    /**
     * @param int $profitType
     * @return self
     */
    public function setProfitType($profitType);

    /**
     * @return int
     */
    public function getProfitValue();

    /**
     * @param int $profitValue
     * @return self
     */
    public function setProfitValue($profitValue);

    /**
     * @return array
     */
    public function getPublishers();

    /**
     * @param array $publishers
     * @return self
     */
    public function setPublishers($publishers);

    /**
     * @return int
     */
    public function getPosition();

    /**
     * @param int $position
     * @return self
     */
    public function setPosition($position);

    /**
     * @return int
     */
    public function getRotationWeight();

    /**
     * @param int $rotationWeight
     * @return self
     */
    public function setRotationWeight($rotationWeight);

    /**
     * @return int
     */
    public function getPriority();

    /**
     * @param int $priority
     * @return self
     */
    public function setPriority($priority);

    /**
     * @return array
     */
    public function getWaterfalls();

    /**
     * @param array $waterfalls
     * @return self
     */
    public function setWaterfalls($waterfalls);

    /**
     * @return LibraryVideoDemandAdTagInterface
     */
    public function getLibraryVideoDemandAdTag();

    /**
     * @param LibraryVideoDemandAdTagInterface $libraryVideoDemandAdTag
     * @return self
     */
    public function setLibraryVideoDemandAdTag($libraryVideoDemandAdTag);
}