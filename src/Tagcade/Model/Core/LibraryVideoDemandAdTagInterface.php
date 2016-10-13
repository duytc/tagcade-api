<?php


namespace Tagcade\Model\Core;

use Tagcade\Model\ModelInterface;

interface LibraryVideoDemandAdTagInterface extends ModelInterface
{
    /**
     * @return mixed
     */
    public function getTagURL();

    /**
     * @param mixed $tagURL
     * @return self
     */
    public function setTagURL($tagURL);

    /**
     * @return mixed
     */
    public function getName();

    /**
     * @param mixed $name
     * @return self
     */
    public function setName($name);

    /**
     * @return mixed
     */
    public function getTimeout();

    /**
     * @param mixed $timeout
     * @return self
     */
    public function setTimeout($timeout);

    /**
     * @return mixed
     */
    public function getTargeting();

    /**
     * @param mixed $targeting
     * @return self
     */
    public function setTargeting($targeting);

    /**
     * @return float
     */
    public function getSellPrice();

    /**
     * @param float $sellPrice
     * @return self
     */
    public function setSellPrice($sellPrice);

    /**
     * @return VideoDemandPartnerInterface
     */
    public function getVideoDemandPartner();

    /**
     * @param VideoDemandPartnerInterface $videoDemandPartner
     * @return self
     */
    public function setVideoDemandPartner(VideoDemandPartnerInterface $videoDemandPartner);

    /**
     * @return mixed
     */
    public function getVideoDemandAdTags();

    /**
     * @return mixed
     */
    public function getWaterfallPlacementRules();

    /**
     * @param array $waterfallPlacementRules
     * @return self
     */
    public function setWaterfallPlacementRules($waterfallPlacementRules);

    /**
     * @return int
     */
    public function getLinkedCount();
}