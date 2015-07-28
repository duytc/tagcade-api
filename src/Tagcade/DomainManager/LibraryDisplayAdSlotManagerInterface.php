<?php

namespace Tagcade\DomainManager;


use Tagcade\Model\Core\LibraryDisplayAdSlotInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface LibraryDisplayAdSlotManagerInterface {
    /**
     * @see \Tagcade\DomainManager\ManagerInterface
     *
     * @param LibraryDisplayAdSlotInterface|string $entity
     * @return bool
     */
    public function supportsEntity($entity);

    /**
     * @param LibraryDisplayAdSlotInterface $libraryAdSlot
     * @return void
     */
    public function save(LibraryDisplayAdSlotInterface $libraryAdSlot);

    /**
     * @param LibraryDisplayAdSlotInterface $libraryAdSlot
     * @return void
     */
    public function delete(LibraryDisplayAdSlotInterface $libraryAdSlot);

    /**
     * @return LibraryDisplayAdSlotInterface
     */
    public function createNew();

    /**
     * @param int $id
     * @return LibraryDisplayAdSlotInterface|null
     */
    public function find($id);

    /**
     * @param int|null $limit
     * @param int|null $offset
     * @return LibraryDisplayAdSlotInterface[]
     */
    public function all($limit = null, $offset = null);

    /**
     * @param PublisherInterface $publisher
     * @param int|null $limit
     * @param int|null $offset
     * @return LibraryDisplayAdSlotInterface[]
     */
    public function getLibraryDisplayAdSlotsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);
} 