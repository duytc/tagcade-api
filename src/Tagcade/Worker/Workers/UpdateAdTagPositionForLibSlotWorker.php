<?php


namespace Tagcade\Worker\Workers;


use Doctrine\ORM\EntityManagerInterface;
use stdClass;
use Tagcade\DomainManager\LibraryDisplayAdSlotManagerInterface;
use Tagcade\Entity\Core\AdTag;
use Tagcade\Entity\Core\LibraryDisplayAdSlot;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\LibraryDisplayAdSlotInterface;
use Tagcade\Repository\Core\AdTagRepositoryInterface;
use Tagcade\Repository\Core\LibraryDisplayAdSlotRepositoryInterface;
use Tagcade\Service\Core\AdTag\AdTagPositionEditorInterface;

class UpdateAdTagPositionForLibSlotWorker
{
    /**
     * @var AdTagPositionEditorInterface
     */
    private $positionEditor;

    /**
     * @var LibraryDisplayAdSlotRepositoryInterface
     */
    private $libraryDisplayAdSlotRepository;

    /**
     * @var AdTagRepositoryInterface
     */
    private $adTagRepository;
    /**
     * @var EntityManagerInterface
     */
    private $em;

    function __construct(EntityManagerInterface $em , AdTagPositionEditorInterface $positionEditor)
    {
        $this->positionEditor = $positionEditor;
        $this->em = $em;

        $this->libraryDisplayAdSlotRepository = $this->em->getRepository(LibraryDisplayAdSlot::class);
        $this->adTagRepository = $this->em->getRepository(AdTag::class);
    }

    /**
     * @param stdClass $param
     */
    public function updateAdTagPositionForLibSlot(StdClass $param)
    {
        $libAdSlotId = filter_var($param->libSlotId, FILTER_VALIDATE_INT);
        $adTagId = $param->adTagId;
        $position = filter_var($param->position, FILTER_VALIDATE_INT);

        $libAdSlot = $this->libraryDisplayAdSlotRepository->find($libAdSlotId);
        if (!$libAdSlot instanceof LibraryDisplayAdSlotInterface) {
            throw new InvalidArgumentException(sprintf('not found any lib slot with id %s', $libAdSlotId));
        }

        $adTag = $this->adTagRepository->find($adTagId);
        if (!$adTag instanceof AdTagInterface) {
            throw new InvalidArgumentException(sprintf('not found any ad tag with id %s', $adTagId));
        }

        //update all referenced AdTags if they are shared ad slot library
        $referencedTags = $this->adTagRepository->getAdTagsByLibraryAdSlotAndRefId($libAdSlot, $adTag->getRefId());

        $countRefTag = 0;
        /**
         * @var AdTagInterface $refTag
         */
        foreach($referencedTags as $refTag) {
            $refTag->setPosition($position);
            $this->em->merge($refTag);
            $countRefTag++;

            if ($countRefTag % 100 == 0 && $countRefTag != 0) {
                $this->em->flush();
            }
        }

        $this->em->flush();
    }
}