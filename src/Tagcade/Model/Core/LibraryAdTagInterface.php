<?php

namespace Tagcade\Model\Core;


use Doctrine\Common\Collections\ArrayCollection;
use Tagcade\Model\ModelInterface;

interface LibraryAdTagInterface extends  ModelInterface{
    /**
     * @param $id
     * @return mixed
     */
    public function setId($id);

    /**
     * @return mixed
     */
    public function getHtml();

    /**
     * @param $html
     * @return mixed
     */
    public function setHtml($html);


    /**
     * @param AdNetworkInterface $adNetwork
     * @return mixed
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
     * @return mixed
     */
    public function setVisible($visible);

    /**
     * @return AdTagInterface[]
     */
    public function getAdTags();

    /**
     * This indicate ad tag type: image, custom, etc..
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

    /**
     * @return LibrarySlotTagInterface
     */
    public function getLibSlotTags();

    /**
     * @param LibrarySlotTagInterface $libSlotTags
     */
    public function setLibSlotTags($libSlotTags);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param mixed $name
     */
    public function setName($name);
}