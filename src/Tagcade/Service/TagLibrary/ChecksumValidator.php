<?php

namespace Tagcade\Service\TagLibrary;


use Doctrine\ORM\PersistentCollection;
use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Repository\Core\AdSlotRepositoryInterface;

class ChecksumValidator implements ChecksumValidatorInterface
{
    /**
     * @var AdSlotRepositoryInterface
     */
    protected $adSlotRepository;
    function __construct(AdSlotRepositoryInterface $adSlotRepository)
    {
        $this->adSlotRepository = $adSlotRepository;
    }

    /**
     * Validate that all AdSlots that created from the same LibraryAdSlot must have the same Checksum value
     * @param BaseAdSlotInterface $originalAdSlot
     * @param $copies array
     */
    public function validateAdSlotSynchronization(BaseAdSlotInterface $originalAdSlot, $copies = null)
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

    public function validateAllAdSlotsSynchronized(array $adSlots) {
        if (count($adSlots) < 1) {
            return;
        }

        $baseAdSlot = current($adSlots);

        $this->validateAdSlotSynchronization($baseAdSlot, $adSlots);
    }

    private function validateSingleAdSlot(BaseAdSlotInterface $adSlot)
    {
        $coReferencedAdSlots = $this->adSlotRepository->getCoReferencedAdSlots($adSlot->getLibraryAdSlot());
        if($coReferencedAdSlots instanceof PersistentCollection) $coReferencedAdSlots = $coReferencedAdSlots->toArray();
        if($coReferencedAdSlots === null || empty($coReferencedAdSlots)) return;

        $this->validateAdSlotSynchronization($adSlot, $coReferencedAdSlots);
    }
}