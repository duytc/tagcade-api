<?php

namespace Tagcade\Model\Core;

use Tagcade\Model\ModelInterface;

interface AdTagInterface extends ModelInterface
{
    /**
     * @param mixed $id
     */
    public function setId($id);

    /**
     * @return AdSlotInterface|null
     */
    public function getAdSlot();

    /**
     * @return int|null
     */
    public function getAdSlotId();

    /**
     * @param AdSlotInterface $adSlot
     * @return self
     */
    public function setAdSlot(AdSlotInterface $adSlot);

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
     * @return int|null
     */
    public function getPosition();

    /**
     * @param int $position
     * @return self
     */
    public function setPosition($position);

    /**
     * @return bool
     */
    public function isActive();

    /**
     * @param $boolean
     * @return $this
     */
    public function setActive($boolean);

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
     */
    public function setRotation($rotation);

    /**
     * get current rotation
     * @return int
     */
    public function getRotation();

    /**
     * get AdType
     * @return int
     */
    public function getAdType();

    /**
     * set AdType
     * @param int $adType
     */
    public function setAdType($adType);

    /**
     * get Descriptor as json_array
     * @return array
     */
    public function getDescriptor();

    /**
     * set Descriptor formatted as json_array
     * @param array $descriptor
     */
    public function setDescriptor($descriptor);
}