<?php

namespace Tagcade\DomainManager;

use Tagcade\Model\Core\LibraryDynamicAdSlotInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface LibraryDynamicAdSlotManagerInterface
{
    /**
     * @see \Tagcade\DomainManager\ManagerInterface
     *
     * @param LibraryDynamicAdSlotInterface|string $entity
     * @return bool
     */
    public function supportsEntity($entity);

    /**
     * @param LibraryDynamicAdSlotInterface $libraryDynamicAdSlot
     * @return void
     */
    public function save(LibraryDynamicAdSlotInterface $libraryDynamicAdSlot);

    /**
     * @param LibraryDynamicAdSlotInterface $libraryDynamicAdSlot
     * @return void
     */
    public function delete(LibraryDynamicAdSlotInterface $libraryDynamicAdSlot);

    /**
     * @return LibraryDynamicAdSlotInterface
     */
    public function createNew();

    /**
     * @param int $id
     * @return LibraryDynamicAdSlotInterface|null
     */
    public function find($id);

    /**
     * @param int|null $limit
     * @param int|null $offset
     * @return LibraryDynamicAdSlotInterface[]
     */
    public function all($limit = null, $offset = null);

    /**
     * @param PublisherInterface $publisher
     * @param int|null $limit
     * @param int|null $offset
     * @return LibraryDynamicAdSlotInterface[]
     */
    public function getLibraryDynamicAdSlotsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);

}