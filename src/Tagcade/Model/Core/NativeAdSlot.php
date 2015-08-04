<?php

namespace Tagcade\Model\Core;

use Doctrine\Common\Collections\ArrayCollection;
use Tagcade\Entity\Core\AdSlotAbstract;
use Tagcade\Model\Core\SiteInterface;

class NativeAdSlot extends AdSlotAbstract implements NativeAdSlotInterface, ReportableAdSlotInterface
{
    protected $id;

    /**
     * @var LibraryDynamicAdSlotInterface[]
     */
    protected $defaultLibraryDynamicAdSlots;
    /**
     * @param string $name
     */



    /**
     * @inheritdoc
     */
    public function getAdTags()
    {
        if (null === $this->adTags) {
            $this->adTags = new ArrayCollection();
        }

        return $this->adTags;
    }

    /**
     * @param ArrayCollection $adTags
     * @return $this
     */
    public function setAdTags($adTags)
    {
        $this->adTags = $adTags;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return self::TYPE_NATIVE;
    }


    public function __toString()
    {
        return $this->id . $this->getName();
    }


    /**
     * @return LibraryDynamicAdSlotInterface[]
     */
    public function getDefaultLibraryDynamicAdSlots()
    {
        return $this->defaultLibraryDynamicAdSlots;
    }

    /**
     * @param LibraryDynamicAdSlotInterface[] $defaultLibraryDynamicAdSlots
     * @return $this
     */
    public function setDefaultLibraryDynamicAdSlots($defaultLibraryDynamicAdSlots)
    {
        $this->defaultLibraryDynamicAdSlots = $defaultLibraryDynamicAdSlots;
        return $this;
    }

    /**
     * @return DynamicAdSlotInterface[]
     */
    public function defaultDynamicAdSlots()
    {
        $dynamicAdSlots = [];

        $libraryDynamicAdSlots = $this->getDefaultLibraryDynamicAdSlots();

        if(null == $libraryDynamicAdSlots) return $dynamicAdSlots;

        foreach($libraryDynamicAdSlots as $libraryDynamicAdSlot){
            $temp = $libraryDynamicAdSlot->getAdSlots();
            if($temp->count() < 1) continue;

            $dynamicAdSlots = array_merge($dynamicAdSlots, $temp->toArray());
        }

        return array_unique($dynamicAdSlots);
    }

    /**
     * @return string
     */
    public function checkSum()
    {
        $array = array(
            $this->getType(),
            $this->getLibraryAdSlot()->getId()
        );

        $adTags = $this->getAdTags()->toArray();

        usort($adTags, function(AdTagInterface $a, AdTagInterface $b) {
            return ($a->getId() < $b->getId()) ? -1 : 1;
        });

        /** @var AdTagInterface $t */
        foreach($adTags as $t){
            $array[] =  $t->checkSum();
        }

        return md5(serialize($array));
    }
}