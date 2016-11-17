<?php

namespace Tagcade\Service\TagLibrary;


use Doctrine\ORM\PersistentCollection;
use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\DynamicAdSlotInterface;
use Tagcade\Repository\Core\AdSlotRepositoryInterface;

class ChecksumValidator implements ChecksumValidatorInterface
{
    /** @var AdSlotRepositoryInterface */
    protected $adSlotRepository;

    function __construct(AdSlotRepositoryInterface $adSlotRepository)
    {
        $this->adSlotRepository = $adSlotRepository;
    }

    /**
     * Validate that all AdSlots that created from the same LibraryAdSlot must have the same Checksum value
     * @param BaseAdSlotInterface $originalAdSlot
     */
    public function validateAdSlotSynchronization(BaseAdSlotInterface $originalAdSlot)
    {
        if ($originalAdSlot instanceof DynamicAdSlotInterface) {
            return;
        }

        $libraryAdSlot = $originalAdSlot->getLibraryAdSlot();

        if ($originalAdSlot->checkSum() !== $libraryAdSlot->checkSum()) {
            throw new RuntimeException(sprintf('%s is created from %s but it seems that their data are not synced', $originalAdSlot->getName(), $libraryAdSlot->getName()));
        }
    }

    public function validateAllAdSlotsSynchronized(array $adSlots)
    {
        if (count($adSlots) < 2) {
            return;
        }

        foreach($adSlots as $adSlot) {
            $this->validateAdSlotSynchronization($adSlot);
        }
    }
}