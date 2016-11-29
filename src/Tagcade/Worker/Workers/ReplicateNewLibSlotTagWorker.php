<?php


namespace Tagcade\Worker\Workers;


use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\PersistentCollection;
use InvalidArgumentException;
use stdClass;
use Tagcade\Entity\Core\AdTag;
use Tagcade\Entity\Core\LibrarySlotTag;
use Tagcade\Entity\Core\VideoDemandAdTag;
use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\LibraryAdTagInterface;
use Tagcade\Model\Core\LibrarySlotTagInterface;
use Tagcade\Model\Core\VideoDemandAdTagInterface;
use Tagcade\Repository\Core\LibrarySlotTagRepositoryInterface;
use Tagcade\Repository\Core\VideoDemandAdTagRepositoryInterface;
use Tagcade\Service\Core\VideoDemandAdTag\AutoPauseServiceInterface;
use Tagcade\Service\TagLibrary\ChecksumValidatorInterface;

class ReplicateNewLibSlotTagWorker
{
    const DEFAULT_BATCH_SIZE = 100;
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var LibrarySlotTagRepositoryInterface
     */
    private $libSlotTagRepository;

    /**
     * @var ChecksumValidatorInterface
     */
    private $checksumValidator;


    function __construct(EntityManagerInterface $em, ChecksumValidatorInterface $checksumValidator)
    {
        $this->em = $em;
        $this->checksumValidator = $checksumValidator;
        $this->libSlotTagRepository = $this->em->getRepository(LibrarySlotTag::class);
    }

    /**
     * @param stdClass $param
     */
    public function replicateNewLibSlotTag(StdClass $param)
    {
        $libSlotTagId = $param->id;
        $libSlotTag = $this->libSlotTagRepository->find($libSlotTagId);

        if (!$libSlotTag instanceof LibrarySlotTagInterface) {
            throw new \Tagcade\Exception\InvalidArgumentException(sprintf('not found any lib slot tag with id %s', $libSlotTagId));
        }

        //check if the Library Slot has been referred by any Slot
        $adSlots = $libSlotTag->getLibraryAdSlot()->getAdSlots();
        if (null === $adSlots) return null; // no slot refers to this library

        if ($adSlots instanceof PersistentCollection) $adSlots = $adSlots->toArray();

        $createdAdTags = [];

        $this->em->getConnection()->beginTransaction();

        try {
            /** @var BaseAdSlotInterface $adSlot */
            foreach ($adSlots as $index=>$adSlot) {
                $newAdTag = new AdTag();
                $newAdTag->setAdSlot($adSlot);
                $newAdTag->setRefId($libSlotTag->getRefId());
                $newAdTag->setLibraryAdTag($libSlotTag->getLibraryAdTag());
                $newAdTag->setFrequencyCap($libSlotTag->getFrequencyCap());
                $newAdTag->setPosition($libSlotTag->getPosition());
                $newAdTag->setRotation($libSlotTag->getRotation());
                $newAdTag->setActive($libSlotTag->isActive());
                $newAdTag->setImpressionCap($libSlotTag->getImpressionCap());
                $newAdTag->setNetworkOpportunityCap($libSlotTag->getNetworkOpportunityCap());
                $this->em->persist($newAdTag);

                $adSlot->getAdTags()->add($newAdTag);
                $this->em->merge($adSlot);
                if ($index % self::DEFAULT_BATCH_SIZE === 0 && $index !== 0) {
                    $this->em->flush();
                }

                $createdAdTags[] = $newAdTag;
            }

            $this->em->flush();

            $this->checksumValidator->validateAllAdSlotsSynchronized($adSlots);

            $this->em->getConnection()->commit();

        } catch (\Exception $ex) {
            $this->em->getConnection()->rollback();
            throw new RuntimeException($ex);
        }
    }
}