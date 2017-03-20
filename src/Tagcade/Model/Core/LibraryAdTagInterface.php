<?php

namespace Tagcade\Model\Core;


use Doctrine\ORM\PersistentCollection;
use Tagcade\Model\ModelInterface;

interface LibraryAdTagInterface extends ModelInterface
{
    /**
     * @param $id
     * @return self
     */
    public function setId($id);

    /**
     * @return mixed
     */
    public function getHtml();

    /**
     * @param $html
     * @return self
     */
    public function setHtml($html);

    /**
     * @param AdNetworkInterface $adNetwork
     * @return self
     */
    public function setAdNetwork($adNetwork);

    /**
     * @return AdNetworkInterface
     */
    public function getAdNetwork();

    /**
     * @return boolean
     */
    public function getVisible();

    /**
     * @param $visible boolean
     * @return self
     */
    public function setVisible($visible);

    /**
     * @return PersistentCollection
     */
    public function getAdTags();

    public function addAdTag(AdTagInterface $adTag);

    /**
     * This indicate ad tag type: image, custom, etc..
     * get AdType
     * @return int
     */
    public function getAdType();

    /**
     * set AdType
     * @param int $adType
     * @return self
     */
    public function setAdType($adType);

    /**
     * get Descriptor as json_array
     * @return array
     */
    public function getDescriptor();

    /**
     * set Descriptor formatted as json_array
     * @param array $inBannerDescriptor
     * @return self
     */
    public function setDescriptor($inBannerDescriptor);

    /**
     * get Descriptor as json_array
     * @return array
     */
    public function getInBannerDescriptor();

    /**
     * set Descriptor formatted as json_array
     * @param array $inBannerDescriptor
     * @return self
     */
    public function setInBannerDescriptor($inBannerDescriptor);

    /**
     * @return LibrarySlotTagInterface[]
     */
    public function getLibSlotTags();

    /**
     * @param LibrarySlotTagInterface[] $libSlotTags
     * @return self
     */
    public function setLibSlotTags($libSlotTags);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param mixed $name
     * @return self
     */
    public function setName($name);

    /**
     * @return int
     */
    public function getAssociatedTagCount();
}