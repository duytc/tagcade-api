<?php

namespace Tagcade\DomainManager;

use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use ReflectionClass;
use Tagcade\Model\Core\BaseLibraryAdSlotInterface;
use Tagcade\Model\Core\LibraryDisplayAdSlotInterface;
use Tagcade\Model\Core\LibrarySlotTagInterface;
use Tagcade\Model\ModelInterface;
use Tagcade\Repository\Core\LibrarySlotTagRepositoryInterface;
use Tagcade\Service\TagLibrary\ReplicatorInterface;
use Tagcade\Worker\Manager;

class LibrarySlotTagManager implements LibrarySlotTagManagerInterface
{
    protected $em;
    protected $repository;

    /** @var ReplicatorInterface */
    protected $replicator;
    /**
     * @var Manager
     */
    protected $manager;

    public function __construct(EntityManagerInterface $em, LibrarySlotTagRepositoryInterface $repository, Manager $manager)
    {
        $this->em = $em;
        $this->repository = $repository;
        $this->manager = $manager;
    }

    public function setReplicator(ReplicatorInterface $replicator)
    {
        $this->replicator = $replicator;
    }

    /**
     * @inheritdoc
     */
    public function supportsEntity($entity)
    {
        return is_subclass_of($entity, LibrarySlotTagInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function save(ModelInterface $librarySlotTag)
    {
        if (!$librarySlotTag instanceof LibrarySlotTagInterface) throw new InvalidArgumentException('expect LibrarySlotTagInterface object');

        $newSlotTag = $librarySlotTag->getId() === null;

        $this->em->persist($librarySlotTag);

        // support "auto increase position" feature: update for all referenced ad tags
        if ($librarySlotTag->getAutoIncreasePosition() && $librarySlotTag->getLibraryAdSlot() instanceof LibraryDisplayAdSlotInterface) {
            // increase for library slot tag
            $this->autoIncreasePositionForRelatedLibrarySlotTags($librarySlotTag);
        }

        //make sure libSlotTag is inserted before adTags
        $this->em->flush();

        // replicate Existing LibrarySlotTag To All Referenced AdTags
        $adSlotLib = $librarySlotTag->getLibraryAdSlot();

        if ($newSlotTag === true) {
            $this->manager->replicateNewLibSlotTag($librarySlotTag->getId());
//            $this->replicator->replicateNewLibrarySlotTagToAllReferencedAdSlots($librarySlotTag);
        }

        if ($librarySlotTag->getAutoIncreasePosition() && $adSlotLib instanceof LibraryDisplayAdSlotInterface) {
            foreach($adSlotLib->getLibSlotTags() as $librarySlotTag) {
                $this->manager->replicateExistingLibSlotTag($librarySlotTag->getId());
            }
//            $this->replicator->replicateExistingLibrarySlotTagsToAllReferencedAdTags($adSlotLib->getLibSlotTags()->toArray());
        } else {
//            $this->replicator->replicateExistingLibrarySlotTagToAllReferencedAdTags($librarySlotTag);
            $this->manager->replicateExistingLibSlotTag($librarySlotTag->getId());
        }
    }

    /**
     * @inheritdoc
     */
    public function delete(ModelInterface $librarySlotTag)
    {
        if (!$librarySlotTag instanceof LibrarySlotTagInterface) throw new InvalidArgumentException('expect LibrarySlotTagInterface object');

        $this->manager->replicateExistingLibSlotTag($librarySlotTag->getId(), true);
//        $this->replicator->replicateExistingLibrarySlotTagToAllReferencedAdTags($librarySlotTag, true);

        $libraryAdSlot = $librarySlotTag->getLibraryAdSlot();
        $libraryAdSlot->removeLibSlotTag($librarySlotTag);
        $this->em->merge($libraryAdSlot);
        $this->em->remove($librarySlotTag);
        $this->em->flush();
    }

    /**
     * @inheritdoc
     */
    public function createNew()
    {
        $entity = new ReflectionClass($this->repository->getClassName());
        return $entity->newInstance();
    }

    /**
     * @inheritdoc
     */
    public function find($id)
    {
        return $this->repository->find($id);
    }

    /**
     * @inheritdoc
     */
    public function all($limit = null, $offset = null)
    {
        $criteria = [];
        return $this->repository->findBy($criteria, $orderBy = null, $limit, $offset);
    }

    /**
     * @param BaseLibraryAdSlotInterface $libraryAdSlot
     * @param null $limit
     * @param null $offset
     * @return mixed
     */
    public function getByLibraryAdSlot(BaseLibraryAdSlotInterface $libraryAdSlot, $limit = null, $offset = null)
    {
        return $this->repository->getByLibraryAdSlot($libraryAdSlot, $limit, $offset);
    }

    public function getByLibraryAdSlotAndRefId(BaseLibraryAdSlotInterface $libraryAdSlot, $refId)
    {
        return $this->repository->getByLibraryAdSlotAndRefId($libraryAdSlot, $refId);
    }

    /**
     * @inheritdoc
     */
    public function getByLibraryAdSlotAndDifferRefId(BaseLibraryAdSlotInterface $libraryAdSlot, $refId)
    {
        return $this->repository->getByLibraryAdSlotAndDifferRefId($libraryAdSlot, $refId);
    }

    /**
     * @return EntityManagerInterface
     */
    protected function getEntityManager()
    {
        return $this->em;
    }

    /**
     * auto Increase Position For Related LibrarySlotTags
     *
     * @param LibrarySlotTagInterface $librarySlotTag
     */
    protected function autoIncreasePositionForRelatedLibrarySlotTags(LibrarySlotTagInterface &$librarySlotTag)
    {
        // get libraryAdSlot
        /** @var BaseLibraryAdSlotInterface $libraryAdSlot */
        $libraryAdSlot = $librarySlotTag->getLibraryAdSlot();

        // only support LibraryDisplayAdSlot
        if (!$libraryAdSlot instanceof LibraryDisplayAdSlotInterface) {
            return;
        }

        $newLibSlotTags = [];
        $includedPersistingTag = false;

        // get all old librarySlotTags of current libraryAdSlot
        $oldLibSlotTags = $libraryAdSlot->getLibSlotTags();

        // do auto increasing position
        foreach ($oldLibSlotTags as $oldLibSlotTag) {
            // add if position small than current persisting/updating ad tag
            if ($oldLibSlotTag->getId() != null && $oldLibSlotTag->getPosition() < $librarySlotTag->getPosition()) {
                $newLibSlotTags[] = $oldLibSlotTag;
                continue;
            }

            // add current persisting/updating ad tag to new ad tags array, sure add only one time!!!
            if ($oldLibSlotTag->getId() != null && $oldLibSlotTag->getPosition() == $librarySlotTag->getPosition() && !$includedPersistingTag) {
                $newLibSlotTags[] = $librarySlotTag;
                $includedPersistingTag = true;
            }

            // increase and add if position greater than or equal position of current persisting/updating ad tag
            /** @var LibrarySlotTagInterface $oldLibSlotTag */
            if ($oldLibSlotTag->getId() != null
                && $oldLibSlotTag->getPosition() >= $librarySlotTag->getPosition()
                && $oldLibSlotTag->getId() != $librarySlotTag->getId() // IMPORTANT: sure not update position of current persisting/updating library slot tag!!!
            ) {
                $oldLibSlotTag->setPosition($oldLibSlotTag->getPosition() + 1);

                $newLibSlotTags[] = $oldLibSlotTag;
            }
        }

        // update back to input $librarySlotTag
        $libraryAdSlot->setLibSlotTags($newLibSlotTags);
        $librarySlotTag->setLibraryAdSlot($libraryAdSlot);
    }
}