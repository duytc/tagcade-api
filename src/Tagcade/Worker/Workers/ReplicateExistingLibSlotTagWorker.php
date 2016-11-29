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
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\LibraryAdTagInterface;
use Tagcade\Model\Core\LibrarySlotTagInterface;
use Tagcade\Model\Core\VideoDemandAdTagInterface;
use Tagcade\Repository\Core\AdTagRepositoryInterface;
use Tagcade\Repository\Core\LibrarySlotTagRepositoryInterface;
use Tagcade\Repository\Core\VideoDemandAdTagRepositoryInterface;
use Tagcade\Service\Core\VideoDemandAdTag\AutoPauseServiceInterface;
use Tagcade\Service\TagLibrary\ChecksumValidatorInterface;

class ReplicateExistingLibSlotTagWorker
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
    public function replicateExistingLibSlotTag(StdClass $param)
    {
        $libSlotTagId = $param->id;
        $remove = filter_var($param->remove, FILTER_VALIDATE_BOOLEAN);
        $libSlotTag = $this->libSlotTagRepository->find($libSlotTagId);

        if (!$libSlotTag instanceof LibrarySlotTagInterface) {
            throw new \Tagcade\Exception\InvalidArgumentException(sprintf('not found any lib slot tag with id %s', $libSlotTagId));
        }

        $this->em->getConnection()->beginTransaction();

        try {

            /** @var AdTagRepositoryInterface $adTagRepository */
            $adTagRepository = $this->em->getRepository(AdTag::class);
            $adTags = $adTagRepository->getAdTagsByLibraryAdSlotAndRefId($libSlotTag->getLibraryAdSlot(), $libSlotTag->getRefId());

            array_walk(
                $adTags,
                function (AdTagInterface $t) use ($libSlotTag, $remove) {

                    if (true === $remove) {
                        $this->em->remove($t);
                        return;
                    }

                    $t->setLibraryAdTag($libSlotTag->getLibraryAdTag());
                    $t->setFrequencyCap($libSlotTag->getFrequencyCap());
                    $t->setPosition($libSlotTag->getPosition());
                    $t->setRotation($libSlotTag->getRotation());
                    $t->setActive($libSlotTag->isActive());
                    $t->setImpressionCap($libSlotTag->getImpressionCap());
                    $t->setNetworkOpportunityCap($libSlotTag->getNetworkOpportunityCap());
                    $this->em->persist($t);
                }
            );

            // if there no any more WaterfallTag refer to this LibraryAdTag then it should be removed as well
            $libraryAdTag = $libSlotTag->getLibraryAdTag();

            if (true === $remove &&
                $libraryAdTag->getAssociatedTagCount() < 1 &&
                count($libraryAdTag->getLibSlotTags()) < 2
            ) {
                $this->em->remove($libraryAdTag);
            }

            $this->em->flush();

            $this->checksumValidator->validateAllAdSlotsSynchronized($libSlotTag->getLibraryAdSlot()->getAdSlots()->toArray());

            $this->em->getConnection()->commit();
        } catch (\Exception $ex) {
            $this->em->getConnection()->rollback();

            throw new RuntimeException($ex);
        }
    }
}