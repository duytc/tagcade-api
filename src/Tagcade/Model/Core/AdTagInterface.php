<?php

namespace Tagcade\Model\Core;

interface AdTagInterface extends PositionInterface
{
    const ACTIVE = 1;
    const PAUSED = 0;
    const AUTO_PAUSED = -1;

    /**
     * @param mixed $id
     * @return self
     */
    public function setId($id);

    /**
     * @return NativeAdSlotInterface|DisplayAdSlotInterface|null
     */
    public function getAdSlot();

    /**
     * @return int|null
     */
    public function getAdSlotId();

    /**
     * @param BaseAdSlotInterface $adSlot
     * @return self
     */
    public function setAdSlot(BaseAdSlotInterface $adSlot);


    /**
     * @return AdNetworkInterface|null
     */
    public function getAdNetwork();

    /**
     * @return int|null
     */
    public function getAdNetworkId();

    /**
     * @param AdNetworkInterface $adNetwork
     * @return self
     */
    public function setAdNetwork(AdNetworkInterface $adNetwork);

    /**
     * @return string|null
     */
    public function getName();

    /**
     * @param string $name
     * @return self
     */
    public function setName($name);

    /**
     * @return string|null
     */
    public function getHtml();

    /**
     * @param string $html
     * @return self
     */
    public function setHtml($html);

    /**
     * @return bool
     */
    public function isActive();

    public function isAutoPaused();

    public function activate();

    /**
     * @param int $activeStatus
     * @return self
     */
    public function setActive($activeStatus);

    /**
     * @param int $frequencyCap
     * @return $this
     */
    public function setFrequencyCap($frequencyCap);

    /**
     * @return int|null
     */
    public function getFrequencyCap();

    /**
     * set rotation
     * @param int $rotation
     * @return self
     */
    public function setRotation($rotation);

    /**
     * get current rotation
     * @return int
     */
    public function getRotation();

    /**
     * @return LibraryAdTagInterface
     */
    public function getLibraryAdTag();

    /**
     * @param LibraryAdTagInterface $libraryAdTag
     * @return self
     */
    public function setLibraryAdTag($libraryAdTag);


    /**
     * @return AdTagInterface[]
     */
    public function getCoReferencedAdTags();

    /**
     * @return string
     */
    public function checkSum();

    /**
     * @return int
     */
    public function getImpressionCap();

    /**
     * @param int $impressionCap
     * @return self
     */
    public function setImpressionCap($impressionCap);

    /**
     * @return int
     */
    public function getNetworkOpportunityCap();

    /**
     * @param int $networkOpportunityCap
     * @return self
     */
    public function setNetworkOpportunityCap($networkOpportunityCap);

    /**
     * @return boolean
     */
    public function getAutoIncreasePosition();

    /**
     * @param bool $autoIncreasePosition
     * @return self
     */
    public function setAutoIncreasePosition($autoIncreasePosition);
}