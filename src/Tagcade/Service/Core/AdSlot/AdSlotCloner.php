<?php


namespace Tagcade\Service\Core\AdSlot;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Tagcade\Bundle\AdminApiBundle\Event\HandlerEventLog;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\DisplayAdSlotInterface;
use Tagcade\Model\Core\NativeAdSlotInterface;
use Tagcade\Model\Core\SiteInterface;

class AdSlotCloner implements AdSlotClonerInterface {
    protected $entityManager;
    protected $eventDispatcher;


    function __construct(EntityManagerInterface $entityManager, EventDispatcherInterface $eventDispatcher)
    {
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
    }


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

        $newLibraryAdSlot = clone $originAdSlot->getLibraryAdSlot();
        $newLibraryAdSlot->setVisible(false);
        $newLibraryAdSlot->setId(null);
        $newLibraryAdSlot->setName($newName);
        $newLibraryAdSlot->setRonAdSlot(null);

        $newAdSlot->setLibraryAdSlot($newLibraryAdSlot);

        $newAdSlot->setAdTags(new ArrayCollection()); // remove referencing ad tags dues to current ad slot clone
        //now clone adTags
        if (null !== $originAdSlot->getAdTags() && count($originAdSlot->getAdTags()) > 0) {
            $oldAdTags = $originAdSlot->getAdTags()->toArray();

            array_walk(
                $oldAdTags,
                function (AdTagInterface $adTag) use(&$newAdSlot){
                    $newAdTag = clone $adTag;
                    $newAdTag->setId(null);
                    $newAdTag->setAdSlot($newAdSlot);
                    $newAdTag->setRefId(uniqid('', true));

                    // clone the LibraryAdTag itself
                    $newLibraryAdTag = clone $adTag->getLibraryAdTag();
                    $newLibraryAdTag->setId(null);
                    $newLibraryAdTag->setVisible(false);
                    $newAdTag->setLibraryAdTag($newLibraryAdTag);

                    $newAdSlot->getAdTags()->add($newAdTag);
                }
            );
        }

        if ($site instanceof SiteInterface) {
            $newAdSlot->setSite($site);
        }

        //persis cloned adSlot
        $this->entityManager->persist($newAdSlot);
        $this->entityManager->flush();

        //dispatch event
        $event = $this->createCloneAdSlotEventLog($originAdSlot, $newAdSlot, $newName);
        $this->eventDispatcher->dispatch(HandlerEventLog::class, $event);
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
}