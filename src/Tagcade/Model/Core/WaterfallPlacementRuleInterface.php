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
     * @return LibraryVideoDemandAdTagInterface
     */
    public function getLibraryVideoDemandAdTag();

    /**
     * @param LibraryVideoDemandAdTagInterface $libraryVideoDemandAdTag
     * @return self
     */
    public function setLibraryVideoDemandAdTag($libraryVideoDemandAdTag);
}