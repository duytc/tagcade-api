<?php

namespace Tagcade\DomainManager;

use Tagcade\Entity\Core\LibraryAdSlotAbstract;
use Tagcade\Exception\LogicException;
use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\BaseLibraryAdSlotInterface;
use Tagcade\Model\Core\DisplayAdSlotInterface;
use Tagcade\Model\Core\DynamicAdSlotInterface;
use Tagcade\Model\Core\LibraryDisplayAdSlotInterface;
use Tagcade\Model\Core\LibraryDynamicAdSlotInterface;
use Tagcade\Model\Core\LibraryNativeAdSlotInterface;
use Tagcade\Model\Core\NativeAdSlotInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Repository\Core\AdSlotRepositoryInterface;
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
    public function save(BaseLibraryAdSlotInterface $libraryAdSlotRepository)
    {
        $this->getManager($libraryAdSlotRepository)->save($libraryAdSlotRepository);
    }

    /**
     * @inheritdoc
     */
    public function delete(BaseLibraryAdSlotInterface $libraryAdSlotRepository)
    {
        $this->getManager($libraryAdSlotRepository)->delete($libraryAdSlotRepository);

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
        $allLibraries = $this->libraryAdSlotRepository->findAll();

        return array_filter($allLibraries, function (LibraryAdSlotAbstract $lib) {
                    return $lib->isVisible() === true;
            }
        );
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
}