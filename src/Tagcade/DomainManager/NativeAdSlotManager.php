<?php

namespace Tagcade\DomainManager;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Rhumsaa\Uuid\Console\Exception;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Tagcade\Bundle\ApiBundle\Event\NewAdSlotFromLibraryEvent;
use Tagcade\DomainManager\Behaviors\ValidateAdSlotSynchronizationTrait;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\Core\NativeAdSlotInterface;
use Tagcade\Model\Core\SiteInterface;
use ReflectionClass;
use Tagcade\Repository\Core\AdSlotRepositoryInterface;
use Tagcade\Repository\Core\NativeAdSlotRepositoryInterface;

class NativeAdSlotManager implements NativeAdSlotManagerInterface
{
    use ValidateAdSlotSynchronizationTrait;
    protected $em;
    protected $repository;
    /**
     * @var AdSlotRepositoryInterface
     */
    private $adSlotRepository;

    public function __construct(EntityManagerInterface $em, NativeAdSlotRepositoryInterface $repository, AdSlotRepositoryInterface $adSlotRepository)
    {
        $this->em = $em;
        $this->repository = $repository;
        $this->adSlotRepository = $adSlotRepository;
    }

    /**
     * @inheritdoc
     */
    public function supportsEntity($entity)
    {
        return is_subclass_of($entity, NativeAdSlotInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function save(NativeAdSlotInterface $nativeAdSlot)
    {
        $this->em->getConnection()->beginTransaction();

        try {

            /** @var NativeAdSlotInterface[] $coReferenceNativeAdSlots */
            $coReferenceNativeAdSlots = $nativeAdSlot->getCoReferencedAdSlots();

            if(null != $coReferenceNativeAdSlots) $coReferenceNativeAdSlots = $coReferenceNativeAdSlots->toArray();

            // if the DisplayAdSlotLib is in the library
            // "$adSlot->getId() == null" is to guarantee that we're creating new instance of DisplayAdSlot, not updating
            if($nativeAdSlot->getLibraryNativeAdSlot()->isVisible() && count($coReferenceNativeAdSlots) > 0 && $nativeAdSlot->getId() == null) {
                $referenceNativeAdSlot = $coReferenceNativeAdSlots[0];

                // clone current existed AdTags
                $adTagsToBeCloned = $referenceNativeAdSlot->getAdTags();

                /** @var AdTagInterface $adTag */
                foreach($adTagsToBeCloned as $adTag){

                    $newAdTag = clone $adTag;
                    $newAdTag->setAdSlot($nativeAdSlot);
                    $newAdTag->setRefId($adTag->getRefId());
                    $nativeAdSlot->getAdTags()->add($newAdTag);
                }

                // Validate synchronization
                $coReferenceNativeAdSlots = array_filter($coReferenceNativeAdSlots, function(BaseAdSlotInterface $as) use ($nativeAdSlot){
                    if($nativeAdSlot->getId() != $as->getId()) return true;
                    else return false;
                });

                $this->prePersistValidate($nativeAdSlot, $coReferenceNativeAdSlots);
            }

            $this->em->persist($nativeAdSlot);
            $this->em->getConnection()->commit();

            $this->em->flush();

        } catch (Exception $e){
            $this->em->getConnection()->rollBack();
            throw $e;
        }

    }

    /**
     * @inheritdoc
     */
    public function delete(NativeAdSlotInterface $nativeAdSlot)
    {
        $libraryNativeAdSlot = $nativeAdSlot->getLibraryNativeAdSlot();
        //1. Remove library if visible = false and co-referenced slots less than 2
        if(!$libraryNativeAdSlot->isVisible() && count($nativeAdSlot->getCoReferencedAdSlots()) < 2 ) {
            $this->em->remove($libraryNativeAdSlot); // resulting cascade remove this ad slot
        }
        else {
            // 2. If the tag is in library then we only remove the tag itself, not the library.
            $this->em->remove($nativeAdSlot);
        }

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
        return $this->repository->findBy($criteria = [], $orderBy = null, $limit, $offset);
    }

    /**
     * @inheritdoc
     */
    public function getNativeAdSlotsForSite(SiteInterface $site, $limit = null, $offset = null)
    {
        return $this->adSlotRepository->getNativeAdSlotsForSite($site, $limit, $offset);
    }

    /**
     * @inheritdoc
     */
    public function getNativeAdSlotsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        return $this->adSlotRepository->getNativeAdSlotsForPublisher($publisher, $limit, $offset);
    }

    public function persistAndFlush(NativeAdSlotInterface $adSlot)
    {
        $this->em->persist($adSlot);
        $this->em->flush();
    }
}