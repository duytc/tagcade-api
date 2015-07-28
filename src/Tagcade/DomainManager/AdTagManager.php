<?php

namespace Tagcade\DomainManager;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use ReflectionClass;
use Tagcade\DomainManager\Behaviors\ValidateAdSlotSynchronizationTrait;
use Tagcade\Entity\Core\AdSlotLibAbstract;
use Tagcade\Entity\Core\AdTag;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\BaseAdSlotLibInterface;
use Tagcade\Model\Core\BaseLibraryAdSlotInterface;
use Tagcade\Model\Core\DisplayAdSlotInterface;
use Tagcade\Model\Core\NativeAdSlotInterface;
use Tagcade\Model\Core\ReportableAdSlotInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Core\AdTagRepositoryInterface;

class AdTagManager implements AdTagManagerInterface
{
    use ValidateAdSlotSynchronizationTrait;
    protected $em;
    protected $repository;

    public function __construct(EntityManagerInterface $em, AdTagRepositoryInterface $repository)
    {
        $this->em = $em;
        $this->repository = $repository;
    }

    /**
     * @inheritdoc
     */
    public function supportsEntity($entity)
    {
        return is_subclass_of($entity, AdTagInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function save(AdTagInterface $adTag)
    {
        $adSlot = $adTag->getAdSlot();
        $adSlotLib = $adSlot->getLibraryAdSlot();

        // use transaction only if ad slot library is set
        if ($adSlotLib instanceof BaseLibraryAdSlotInterface && $adSlotLib->isVisible()) {
            // start transaction here
            $this->em->getConnection()->beginTransaction();
            try {

                if (!$adTag->getLibraryAdTag()->getVisible()) {
                    $adTag->getLibraryAdTag()->setVisible(true); // when slot is shared then its tag is shared as well
                }

                $referencedAdSlots = $adSlot->getCoReferencedAdSlots();

                if($adTag->getId() == null) $adTag->setRefId(uniqid('', true));

                //Persist ad tag to the Co-Referenced AdSlots
                foreach ($referencedAdSlots as $referencedAdSlot) {
                    if ($adTag->getAdSlotId() == $referencedAdSlot->getId()) {
                        continue;
                    }

                    //we're updating AdTag. This leads to updating tags in other ad slot that has the same library id
                    if($adTag->getId() != null){
                        $adTags = $this->getAdTagsByAdSlotAndRefId($referencedAdSlot, $adTag->getRefId());
                        array_walk(
                            $adTags,
                            function(AdTagInterface $t) use ($adTag) {
                                $t->setName($adTag->getName());
                                $t->setActive($adTag->isActive());
                                $t->setPosition($adTag->getPosition());
                                $t->setRotation($adTag->getRotation());
                                $t->setFrequencyCap($adTag->getFrequencyCap());
                            }
                        );
                    }
                    //we're creating a new AdTag
                    else {
                        $newAdTag = clone $adTag;
                        $newAdTag->setId(null); // make sure new id is generated; other fields are identical
                        $newAdTag->setAdSlot($referencedAdSlot);
                        $newAdTag->setRefId($adTag->getRefId());

                        $this->em->persist($newAdTag);
                    }
                }


                //validate synchronization
                $this->preUpdateValidate($adTag->getAdSlot());

                //persist the updating AdTag
                $this->em->persist($adTag);
                $this->em->getConnection()->commit();
            } catch (Exception $e) {
                $this->em->getConnection()->rollback();
                throw $e;
            }
            // end transaction
        }
        else {
            $adTag->setRefId(uniqid('', true));
            $this->em->persist($adTag);
        }

        $this->em->flush();
    }

    /**
     * @inheritdoc
     */
    public function delete(AdTagInterface $adTag)
    {
        // start transaction here
        $this->em->getConnection()->beginTransaction();

        try {
            $libraryAdTag = $adTag->getLibraryAdTag();
            //1. Remove library if visible = false and co-referenced tags less than 2
            if(!$libraryAdTag->getVisible() && count($adTag->getCoReferencedAdTags()) < 2 ) {
                $this->em->remove($libraryAdTag); // resulting cascade remove this ad tag
            }
//            else {
//            // 2. If the tag is in library then we only remove the tag itself, not the library.
//                $this->em->remove($adTag);
//            }

            // 3. if the ad slot containing this ad tag is in library, then we have to remove all ad tags in other ad slots as well
            // these ad tags must be shared from the same tag library record with visible = false or true
            $adSlot = $adTag->getAdSlot();
            if ($adSlot instanceof DisplayAdSlotInterface || $adSlot instanceof NativeAdSlotInterface) {
                $adSlotLib = $adSlot->getLibraryAdSlot();

                if ($adSlotLib->isVisible()) { // telling the slot is in library
                    $referencedAdSlots = $adSlot->getCoReferencedAdSlots();


                    foreach($referencedAdSlots as $referredAdSlot){
                        $adTags = $this->getAdTagsByAdSlotAndRefId($referredAdSlot, $adTag->getRefId());
                        array_map(function(AdTagInterface $t){
                            $this->em->remove($t);
                        },$adTags);
//                        foreach($adTags as $t){
//                            $this->em->remove($t);
//                        }
                    }
                }
            }

            $this->em->getConnection()->commit();
        } catch (Exception $e) {
            $this->em->getConnection()->rollback();
            throw $e;
        }
        // end transaction

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
    public function getAdTagsForAdSlot(ReportableAdSlotInterface $adSlot, $limit = null, $offset = null)
    {
        return $this->repository->getAdTagsForAdSlot($adSlot, $limit, $offset);
    }

    public function getAdTagsForSite(SiteInterface $site, $filterActive = false, $limit = null, $offset = null)
    {
        $adTags = $this->repository->getAdTagsForSite($site, $limit, $offset);

        if (true === $filterActive) {
            return array_filter(
                $adTags,
                function(AdTagInterface $adTag)
                {
                    return $adTag->isActive();
                }
            );
        }

        return $adTags;
    }

    /**
     * @inheritdoc
     */
    public function getAdTagsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        return $this->repository->getAdTagsForPublisher($publisher, $limit, $offset);
    }

    /**
     * @inheritdoc
     */
    public function getAdTagsForAdNetwork(AdNetworkInterface $adNetwork, $limit = null, $offset = null)
    {
        return $this->repository->getAdTagsForAdNetwork($adNetwork, $limit, $offset);
    }

    public function getAdTagsForAdNetworkFilterPublisher(AdNetworkInterface $adNetwork, $limit = null, $offset = null)
    {
        return $this->repository->getAdTagsForAdNetworkFilterPublisher($adNetwork, $limit, $offset);
    }


    public function getAdTagsForAdNetworkAndSite(AdNetworkInterface $adNetwork, SiteInterface $site, $limit = null, $offset = null)
    {
        return $this->repository->getAdTagsForAdNetworkAndSite($adNetwork, $site, $limit, $offset);
    }

    public function getAdTagsForAdNetworkAndSites(AdNetworkInterface $adNetwork, array $sites, $limit = null, $offset = null)
    {
        return $this->repository->getAdTagsForAdNetworkAndSites($adNetwork, $sites, $limit, $offset);
    }

    public function getAdTagsForAdNetworkAndSiteFilterPublisher(AdNetworkInterface $adNetwork, SiteInterface $site, $limit = null, $offset = null)
    {
        return $this->repository->getAdTagsForAdNetworkAndSiteFilterPublisher($adNetwork, $site, $limit, $offset);
    }

    public function updateAdTagStatusForAdNetwork(AdNetworkInterface $adNetwork, $active = true)
    {
        $adTags = $this->getAdTagsForAdNetwork($adNetwork);

        /**
         * @var AdTagInterface $adTag
         */
        foreach($adTags as $adTag) {
            if ($adTag->isActive() !== $active) {
                $adTag->setActive($active);
                $this->em->persist($adTag);
            }
        }

        $this->em->flush();
    }

    /**
     * @param BaseAdSlotInterface $adSlot
     * @param int|null $limit
     * @param int|null $offset
     * @return AdTagInterface[]
     */
    public function getSharedAdTagsForAdSlot(BaseAdSlotInterface $adSlot, $limit = null, $offset = null)
    {
        return $this->repository->getSharedAdTagsForAdSlot($adSlot, $limit, $offset);
    }


    public function getAdTagsByAdSlotAndRefId(BaseAdSlotInterface $adSlot, $refId, $limit = null, $offset = null)
    {
        return $this->repository->getAdTagsByAdSlotAndRefId($adSlot, $refId, $limit, $offset);
    }
}