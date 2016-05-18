<?php

namespace Tagcade\DomainManager;

use Doctrine\ORM\PersistentCollection;
use InvalidArgumentException;
use Tagcade\Exception\LogicException;
use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\BaseLibraryAdSlotInterface;
use Tagcade\Model\Core\LibraryDisplayAdSlotInterface;
use Tagcade\Model\Core\LibraryDynamicAdSlotInterface;
use Tagcade\Model\Core\LibraryNativeAdSlotInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\ModelInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Core\LibraryAdSlotRepositoryInterface;

class LibraryAdSlotManager implements LibraryAdSlotManagerInterface
{
    /**
     * @var LibraryDisplayAdSlotManagerInterface
     */
    private $libraryDisplayAdSlotManager;
    /**
     * @var LibraryNativeAdSlotManagerInterface
     */
    private $libraryNativeAdSlotManager;
    /**
     * @var LibraryDynamicAdSlotManagerInterface
     */
    private $libraryDynamicAdSlotManager;
    /**
     * @var LibraryAdSlotRepositoryInterface
     */
    private $libraryAdSlotRepository;

    public function __construct(LibraryDisplayAdSlotManagerInterface $libraryDisplayAdSlotManager,
                                LibraryNativeAdSlotManagerInterface $libraryNativeAdSlotManager,
                                LibraryDynamicAdSlotManagerInterface $libraryDynamicAdSlotManager,
                                LibraryAdSlotRepositoryInterface $libraryAdSlotRepository
    )
    {
        $this->libraryDisplayAdSlotManager = $libraryDisplayAdSlotManager;
        $this->libraryNativeAdSlotManager = $libraryNativeAdSlotManager;
        $this->libraryDynamicAdSlotManager = $libraryDynamicAdSlotManager;
        $this->libraryAdSlotRepository = $libraryAdSlotRepository;
    }

    /**
     * @inheritdoc
     */
    public function supportsEntity($entity)
    {
        return $this->libraryDisplayAdSlotManager->supportsEntity($entity) || $this->libraryNativeAdSlotManager->supportsEntity($entity) || $this->libraryDynamicAdSlotManager->supportsEntity($entity);
    }

    /**
     * @inheritdoc
     */
    public function save(ModelInterface $libraryAdSlot)
    {
        if (!$libraryAdSlot instanceof BaseLibraryAdSlotInterface) throw new InvalidArgumentException('expect BaseLibraryAdSlotInterface object');

        $this->getManager($libraryAdSlot)->save($libraryAdSlot);
    }

    /**
     * @inheritdoc
     */
    public function delete(ModelInterface $libraryAdSlot)
    {
        if (!$libraryAdSlot instanceof BaseLibraryAdSlotInterface) throw new InvalidArgumentException('expect BaseLibraryAdSlotInterface object');

        $this->getManager($libraryAdSlot)->delete($libraryAdSlot);
    }

    /**
     * @inheritdoc
     */
    public function createNew()
    {
        throw new RuntimeException('Not support create new instance of ad slot via generic AdSlotManager. Use either DisplayAdSlotManager, NativeAdSlotManager or DynamicAdSlotManager');
    }

    /**
     * @inheritdoc
     */
    public function find($id)
    {
        return $this->libraryAdSlotRepository->find($id);
    }

    /**
     * @inheritdoc
     */
    public function all($limit = null, $offset = null)
    {
        return $this->libraryAdSlotRepository->getAllActiveLibraryAdSlots();
    }


    /**
     * @inheritdoc
     */
    public function getAdSlotsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        return $this->libraryAdSlotRepository->getLibraryAdSlotsForPublisher($publisher, $limit, $offset);
    }

    /**
     * @param BaseLibraryAdSlotInterface $libraryAdSlotRepository
     * @return LibraryDisplayAdSlotManagerInterface|LibraryNativeAdSlotManagerInterface|LibraryDynamicAdSlotManagerInterface
     */
    protected function getManager(BaseLibraryAdSlotInterface $libraryAdSlotRepository)
    {
        if ($libraryAdSlotRepository instanceof LibraryDisplayAdSlotInterface) {
            return $this->libraryDisplayAdSlotManager;
        }

        if ($libraryAdSlotRepository instanceof LibraryNativeAdSlotInterface) {
            return $this->libraryNativeAdSlotManager;
        }

        if ($libraryAdSlotRepository instanceof LibraryDynamicAdSlotInterface) {
            return $this->libraryDynamicAdSlotManager;
        }

        throw new LogicException('Do not support manager for this type of ad slot');
    }

    /**
     * @param PublisherInterface $publisher
     * @param int|null $limit
     * @param int|null $offset
     * @return BaseLibraryAdSlotInterface[]
     */
    public function getLibraryDisplayAdSlotsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        return $this->libraryAdSlotRepository->getLibraryDisplayAdSlotsForPublisher($publisher, $limit, $offset);
    }

    /**
     * @param PublisherInterface $publisher
     * @param null $limit
     * @param null $offset
     * @return BaseLibraryAdSlotInterface[]
     */
    public function getLibraryAdSlotsUnusedInRonForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        return $this->libraryAdSlotRepository->getAllLibraryAdSlotsUnusedInRon($publisher->getId(), $limit, $offset);
    }

    /**
     * @param PublisherInterface $publisher
     * @param null $limit
     * @param null $offset
     * @return BaseLibraryAdSlotInterface[]
     */
    public function getLibraryAdSlotsUsedInRonForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        return $this->libraryAdSlotRepository->getAllLibraryAdSlotsUsedInRon($publisher->getId(), $limit, $offset);
    }


    /**
     * @param null|int $limit
     * @param null|int $offset
     * @return BaseLibraryAdSlotInterface[]
     */
    public function getAllLibraryAdSlotsUnusedInRon($limit = null, $offset = null)
    {
        return $this->libraryAdSlotRepository->getAllLibraryAdSlotsUnusedInRon($limit, $offset);
    }

    /**
     * @param null $limit
     * @param null $offset
     * @return BaseLibraryAdSlotInterface[]
     */
    public function getAllLibraryAdSlotsUsedInRon($limit = null, $offset = null)
    {
        return $this->libraryAdSlotRepository->getAllLibraryAdSlotsUsedInRon($limit, $offset);
    }


    /**
     * Get those library ad slots that haven't been referred by any ad slot
     *
     * @param SiteInterface $site
     * @param null $limit
     * @param null $offset
     * @return array
     */
    public function getUnReferencedLibraryAdSlotForSite(SiteInterface $site, $limit = null, $offset = null)
    {
        $result = [];
        $libraryAdSlots = $this->libraryAdSlotRepository->getLibraryAdSlotsForPublisher($site->getPublisher());
        /**
         * @var BaseLibraryAdSlotInterface $libraryAdSlot
         */
        foreach ($libraryAdSlots as $libraryAdSlot) {
            $adSlots = $libraryAdSlot->getAdSlots();
            if ($adSlots instanceof PersistentCollection) $adSlots = $adSlots->toArray();

            if (!is_array($adSlots) || count($adSlots) < 1) {
                $result[] = $libraryAdSlot;
                continue;
            }

            $adSlots = array_filter($adSlots, function (BaseAdSlotInterface $adSlot) use ($site) {
                if ($adSlot->getSite()->getId() === $site->getId()) return true;
                else return false;
            });

            if (count($adSlots) < 1) {
                $result[] = $libraryAdSlot;
            }
        }

        return $result;
    }
}