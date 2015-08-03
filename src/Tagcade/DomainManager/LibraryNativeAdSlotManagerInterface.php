<?php

namespace Tagcade\DomainManager;


use Tagcade\Model\Core\LibraryNativeAdSlotInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface LibraryNativeAdSlotManagerInterface {
    /**
     * @see \Tagcade\DomainManager\ManagerInterface
     *
     * @param LibraryNativeAdSlotInterface|string $entity
     * @return bool
     */
    public function supportsEntity($entity);

    /**
     * @param LibraryNativeAdSlotInterface $adSlot
     * @return void
     */
    public function save(LibraryNativeAdSlotInterface $adSlot);

    /**
     * @param LibraryNativeAdSlotInterface $adSlot
     * @return void
     */
    public function delete(LibraryNativeAdSlotInterface $adSlot);

    /**
     * @return LibraryNativeAdSlotInterface
     */
    public function createNew();

    /**
     * @param int $id
     * @return LibraryNativeAdSlotInterface|null
     */
    public function find($id);

    /**
     * @param int|null $limit
     * @param int|null $offset
     * @return LibraryNativeAdSlotInterface[]
     */
    public function all($limit = null, $offset = null);

    /**
     * @param PublisherInterface $publisher
     * @param int|null $limit
     * @param int|null $offset
     * @return LibraryNativeAdSlotInterface[]
     */
    public function getLibraryNativeAdSlotsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);
}