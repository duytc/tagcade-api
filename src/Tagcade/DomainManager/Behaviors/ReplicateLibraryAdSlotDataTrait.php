<?php

namespace Tagcade\DomainManager\Behaviors;


use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\PersistentCollection;
use Tagcade\Entity\Core\AdTag;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Exception\LogicException;
use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\BaseLibraryAdSlotInterface;
use Tagcade\Model\Core\DisplayAdSlotInterface;
use Tagcade\Model\Core\LibraryDisplayAdSlotInterface;
use Tagcade\Model\Core\LibraryNativeAdSlotInterface;
use Tagcade\Model\Core\LibrarySlotTag;
use Tagcade\Model\Core\LibrarySlotTagInterface;
use Tagcade\Model\Core\NativeAdSlotInterface;

trait ReplicateLibraryAdSlotDataTrait {
    /**
     * Replicate all ad tags of a shared ad slot to other ad slots that refer to the same library
     * @param $libAdSlot
     * @param $adSlot
     *
     * @return BaseAdSlotInterface
     */
    protected function replicateFromLibrarySlotToSingleAdSlot(BaseLibraryAdSlotInterface $libAdSlot, BaseAdSlotInterface &$adSlot)
    {
        if(!$libAdSlot instanceof LibraryDisplayAdSlotInterface && !$libAdSlot instanceof LibraryNativeAdSlotInterface) {
            throw new InvalidArgumentException('expect instance of LibraryDisplayAdSlotInterface or LibraryNativeAdSlotInterface');

        }


        if(!$adSlot instanceof DisplayAdSlotInterface && !$adSlot instanceof NativeAdSlotInterface) {
            throw new InvalidArgumentException('expect instance of DisplayAdSlotInterface or NativeAdSlotInterface');
        }

        if($adSlot->getLibraryAdSlot()->getId() !== $libAdSlot->getId()) {
            throw new LogicException('expect that ad slot must be from the same library ad slot');
        }

        // add new ad slot that refers to a library then we have to replicate all tags in that library to the slot
        $librarySlotTags = $libAdSlot->getLibSlotTags();

        foreach($librarySlotTags as $librarySlotTag) {
            $newAdTag = new AdTag();
            $newAdTag->setAdSlot($adSlot);
            $newAdTag->setRefId($librarySlotTag->getRefId());
            $newAdTag->setLibraryAdTag($librarySlotTag->getLibraryAdTag());
            $newAdTag->setFrequencyCap($librarySlotTag->getFrequencyCap());
            $newAdTag->setPosition($librarySlotTag->getPosition());
            $newAdTag->setRotation($librarySlotTag->getRotation());
            $newAdTag->setActive($librarySlotTag->isActive());

            $adSlot->getAdTags()->add($newAdTag);
        }

        return $adSlot;

    }

    /**
     * add new ad tag to all slots that refer to the same library on persisting new $librarySlotTag
     * @param LibrarySlotTagInterface $librarySlotTag
     * @return AdTagInterface[]|null
     */
    protected function replicateNewLibrarySlotTagToAllReferencedAdSlots(LibrarySlotTagInterface $librarySlotTag)
    {
        //check if the Library Slot has been referred by any Slot
        $adSlots = $librarySlotTag->getLibraryAdSlot()->getAdSlots();
        if(null === $adSlots) return null; // no slot refers to this library

        if($adSlots instanceof PersistentCollection) $adSlots = $adSlots->toArray();

        $createdAdTags = [];
        /**
         * @var BaseAdSlotInterface $adSlot
         */
        foreach($adSlots as $adSlot)
        {
            $newAdTag = new AdTag();
            $newAdTag->setAdSlot($adSlot);
            $newAdTag->setRefId($librarySlotTag->getRefId());
            $newAdTag->setLibraryAdTag($librarySlotTag->getLibraryAdTag());
            $newAdTag->setFrequencyCap($librarySlotTag->getFrequencyCap());
            $newAdTag->setPosition($librarySlotTag->getPosition());
            $newAdTag->setRotation($librarySlotTag->getRotation());
            $newAdTag->setActive($librarySlotTag->isActive());

            $adSlot->getAdTags()->add($newAdTag);
            $this->getEntityManager()->persist($adSlot);

            $createdAdTags[] = $newAdTag;
        }

        return $createdAdTags;
    }

    /**
     * @param LibrarySlotTagInterface $librarySlotTag
     * @param bool $remove true if we are removing $librarySlotTag
     */
    protected function replicateExistingLibrarySlotTagToAllReferencedAdTags(LibrarySlotTagInterface $librarySlotTag, $remove = false)
    {
        $siblingTags = $librarySlotTag->getLibraryAdTag()->getAdTags()->toArray();

        $adTags = array_filter($siblingTags, function(AdTagInterface $adTag) use($librarySlotTag, $librarySlotTag){
            return $adTag->getAdSlot()->getLibraryAdSlot()->getId() == $librarySlotTag->getLibraryAdSlot()->getId() && $librarySlotTag->getRefId() == $adTag->getRefId();
        });

        array_walk(
            $adTags,
            function(AdTagInterface $t) use($librarySlotTag, $remove){

                if (true === $remove) {
                    $this->getEntityManager()->remove($t);
                    return;
                }

                $t->setLibraryAdTag($librarySlotTag->getLibraryAdTag());
                $t->setFrequencyCap($librarySlotTag->getFrequencyCap());
                $t->setPosition($librarySlotTag->getPosition());
                $t->setRotation($librarySlotTag->getRotation());
                $t->setActive($librarySlotTag->isActive());

                $this->getEntityManager()->persist($t);
            }
        );
    }

    /**
     * @return EntityManagerInterface
     */
    protected abstract function getEntityManager();

}