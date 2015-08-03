<?php

namespace Tagcade\Handler;


use Doctrine\Common\Collections\ArrayCollection;
use Tagcade\Bundle\AdminApiBundle\Event\HandlerEventLog;
use Tagcade\DomainManager\DisplayAdSlotManagerInterface;
use Tagcade\DomainManager\NativeAdSlotManagerInterface;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\DisplayAdSlotInterface;
use Tagcade\Model\Core\NativeAdSlotInterface;
use Tagcade\Model\Core\SiteInterface;

trait CloneAdSlotTrait {

    /**
     * clone AdSlot
     * @param BaseAdSlotInterface $originAdSlot
     * @param string $newName
     * @param SiteInterface $site
     */
    public function cloneAdSlot(BaseAdSlotInterface $originAdSlot, $newName, SiteInterface $site = null)
    {
        if (!$originAdSlot instanceof DisplayAdSlotInterface && !$originAdSlot instanceof NativeAdSlotInterface) {
            throw new InvalidArgumentException('expect instance of DisplayAdSlotInterface or NativeAdSlotInterface');
        }
        //clone adSlot
        $newAdSlot = clone $originAdSlot;
        $newAdSlot->setId(null);
        $libraryVisible = $originAdSlot->getLibraryAdSlot()->isVisible();

        $newLibraryAdSlot = clone $originAdSlot->getLibraryAdSlot();
        $newLibraryAdSlot->setVisible(false);
        $newLibraryAdSlot->setId(null);
        $newLibraryAdSlot->setName($newName);

        $newAdSlot->setLibraryAdSlot($newLibraryAdSlot);
        $newAdSlot->setName($newName);

        $newAdSlot->setAdTags(new ArrayCollection()); // remove referencing ad tags dues to current ad slot clone
        //now clone adTags
        if (null !== $originAdSlot->getAdTags() && count($originAdSlot->getAdTags()) > 0) {
            $oldAdTags = $originAdSlot->getAdTags()->toArray();

            array_walk(
                $oldAdTags,
                function (AdTagInterface $adTag) use(&$newAdSlot, $libraryVisible){
                    $newAdTag = clone $adTag;
                    $newAdTag->setId(null);
                    $newAdTag->setAdSlot($newAdSlot);

                    if(!$libraryVisible){
                        $newAdTag->setRefId(uniqid('', true));
                    }

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
        $event = $this->createCloneAdSlotEventLog($originAdSlot, $newAdSlot, $newName);
        $this->dispatchEvent($event);
    }

    /**
     * @param BaseAdSlotInterface $originAdSlot
     * @param BaseAdSlotInterface $newAdSlot
     * @param string $newName
     * @return HandlerEventLog
     */
    private function createCloneAdSlotEventLog(BaseAdSlotInterface $originAdSlot, BaseAdSlotInterface $newAdSlot, $newName)
    {
        $event = new HandlerEventLog('POST', $newAdSlot);

        //add changedFields
        $event->addChangedFields('[clone id]', $originAdSlot->getId(), $newAdSlot->getId());
        $event->addChangedFields('[clone name]', $originAdSlot->getName(), $newName);

        //add affectedEntities
        $event->addAffectedEntityByObject($originAdSlot);

        return $event;
    }

    /**
     * @return NativeAdSlotManagerInterface|DisplayAdSlotManagerInterface
     */
    protected abstract function getDomainManager();

    protected abstract function dispatchEvent($event);
}