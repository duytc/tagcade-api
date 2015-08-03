<?php

namespace Tagcade\DomainManager;

use Tagcade\Model\Core\BaseLibraryAdSlotInterface;
use Tagcade\Model\Core\LibraryAdTagInterface;
use Tagcade\Model\Core\LibrarySlotTagInterface;

interface LibrarySlotTagManagerInterface
{
    /**
     * @see \Tagcade\DomainManager\ManagerInterface
     *
     * @param LibrarySlotTagInterface|string $entity
     * @return bool
     */
    public function supportsEntity($entity);

    /**
     * @param LibrarySlotTagInterface $librarySlotTag
     * @return void
     */
    public function save(LibrarySlotTagInterface $librarySlotTag);

    /**
     * @param LibrarySlotTagInterface $librarySlotTag
     * @return void
     */
    public function delete(LibrarySlotTagInterface $librarySlotTag);

    /**
     * @return LibrarySlotTagInterface
     */
    public function createNew();

    /**
     * @param int $id
     * @return LibrarySlotTagInterface|null
     */
    public function find($id);

    /**
     * @param int|null $limit
     * @param int|null $offset
     * @return LibrarySlotTagInterface[]
     */
    public function all($limit = null, $offset = null);

    /**
     * @param BaseLibraryAdSlotInterface $libraryAdSlot
     * @param null $limit
     * @param null $offset
     * @return mixed
     */
    public function getByLibraryAdSlot(BaseLibraryAdSlotInterface $libraryAdSlot, $limit = null, $offset = null);


    /**
     * @param BaseLibraryAdSlotInterface $libraryAdSlot
     * @param LibraryAdTagInterface $libraryAdTag
     * @param $refId
     * @return LibrarySlotTagInterface|null
     */
    public function getByLibraryAdSlotAndLibraryAdTagAndRefId(BaseLibraryAdSlotInterface $libraryAdSlot, LibraryAdTagInterface $libraryAdTag, $refId);
}