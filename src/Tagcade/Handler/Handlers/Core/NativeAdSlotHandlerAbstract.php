<?php

namespace Tagcade\Handler\Handlers\Core;

use Doctrine\Common\Collections\ArrayCollection;
use Tagcade\Bundle\AdminApiBundle\Event\HandlerEventLog;
use Tagcade\DomainManager\NativeAdSlotManagerInterface;
use Tagcade\Handler\RoleHandlerAbstract;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\NativeAdSlotInterface;
use Tagcade\Model\Core\SiteInterface;

abstract class NativeAdSlotHandlerAbstract extends RoleHandlerAbstract
{
    /**
     * @inheritdoc
     *
     * Auto complete helper method
     *
     * @return NativeAdSlotManagerInterface
     */
    protected function getDomainManager()
    {
        return parent::getDomainManager();
    }

    /**
     * clone NativeAdSlot
     * @param NativeAdSlotInterface $originNativeAdSlot
     * @param string $newName
     * @param SiteInterface $site
     */
    public function cloneNativeAdSlot(NativeAdSlotInterface $originNativeAdSlot, $newName, SiteInterface $site = null)
    {
        //clone adSlot
        $newAdSlot = clone $originNativeAdSlot;
        $newAdSlot->setId(null);

        $newLibraryAdSlot = clone $originNativeAdSlot->getLibraryNativeAdSlot();
        $newLibraryAdSlot->setId(null);
        $newLibraryAdSlot->setReferenceName($newName);
        $newAdSlot->setLibraryNativeAdSlot($newLibraryAdSlot);
        $newAdSlot->setName($newName);

        $newAdSlot->setAdTags(new ArrayCollection()); // remove referencing ad tags dues to current ad slot clone

        //now clone adTags
        if (null !== $originNativeAdSlot->getAdTags() && count($originNativeAdSlot->getAdTags()) > 0) {
            $oldAdTags = $originNativeAdSlot->getAdTags()->toArray();

            array_walk(
                $oldAdTags,
                function (AdTagInterface $adTag) use(&$newAdSlot){
                    $newAdTag = clone $adTag;
                    $newAdTag->setId(null);
                    $newAdTag->setAdSlot($newAdSlot);
                    $newAdTag->setRefId(uniqid('', true));

                    if(!$adTag->getLibraryAdTag()->getVisible()){
                        // clone the LibraryAdTag itself
                        $newLibraryAdTag = clone $adTag->getLibraryAdTag();
                        $newLibraryAdTag->setId(null);
                        $newAdTag->setLibraryAdTag($newLibraryAdTag);
                    }

                    $newAdSlot->getAdTags()->add($newAdTag);
                }
            );
        }

        if ($site instanceof SiteInterface) {
            $newAdSlot->setSite($site);
        }

        //persis cloned adSlot
        $this->getDomainManager()->persistAndFlush($newAdSlot);

        //dispatch event
        $event = $this->createCloneNativeAdSlotEventLog($originNativeAdSlot, $newAdSlot, $newName);
        $this->dispatchEvent($event);
    }

    /**
     * @param NativeAdSlotInterface $originNativeAdSlot
     * @param NativeAdSlotInterface $newNativeAdSlot
     * @param string $newName
     * @return HandlerEventLog
     */
    private function createCloneNativeAdSlotEventLog(NativeAdSlotInterface $originNativeAdSlot, NativeAdSlotInterface $newNativeAdSlot, $newName)
    {
        $event = new HandlerEventLog('POST', $newNativeAdSlot);

        //add changedFields
        $event->addChangedFields('[clone id]', $originNativeAdSlot->getId(), $newNativeAdSlot->getId());
        $event->addChangedFields('[clone name]', $originNativeAdSlot->getName(), $newName);

        //add affectedEntities
        $event->addAffectedEntityByObject($originNativeAdSlot);

        return $event;
    }
}