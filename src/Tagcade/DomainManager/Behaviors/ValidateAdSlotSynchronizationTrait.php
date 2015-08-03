<?php

namespace Tagcade\DomainManager\Behaviors;


use Doctrine\ORM\PersistentCollection;
use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Core\BaseAdSlotInterface;

trait ValidateAdSlotSynchronizationTrait {

    /**
     * Validate that all AdSlots that created from the same LibraryAdSlot must have the same Checksum value
     * @param BaseAdSlotInterface $originalAdSlot
     * @param $copies array
     */
    protected function validateAdSlotSynchronization(BaseAdSlotInterface $originalAdSlot, $copies = null)
    {
        if($copies === null) {
            $this->validateSingleAdSlot($originalAdSlot);
        }
        else {
            /** @var BaseAdSlotInterface $copy */
            foreach($copies as $copy){
                if($originalAdSlot->checkSum() !== $copy->checkSum())
                {
                    throw new RuntimeException(sprintf('%s is created from %s but it seems that their data are not synced', $copy->getName(), $originalAdSlot->getName()));
                }
            }
        }
    }


    private function validateSingleAdSlot(BaseAdSlotInterface $adSlot)
    {
        $coReferencedAdSlots = $adSlot->getCoReferencedAdSlots();
        if($coReferencedAdSlots === null) return;
        if($coReferencedAdSlots instanceof PersistentCollection) $coReferencedAdSlots = $coReferencedAdSlots->toArray();
        if(count($coReferencedAdSlots) < 2) return;
        $originalAdSlot = $coReferencedAdSlots[0];
        foreach($coReferencedAdSlots as $copy){
            if($originalAdSlot->checkSum() !== $copy->checkSum())
            {
                throw new RuntimeException(sprintf('%s is created from %s but it seems that their data are not synced', $copy->getName(), $originalAdSlot->getName()));
            }
        }
    }
}