<?php

namespace Tagcade\DomainManager;

use Tagcade\Model\Core\LibraryAdTagInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface LibraryAdTagManagerInterface
{
    /**
     * @see \Tagcade\DomainManager\ManagerInterface
     *
     * @param LibraryAdTagInterface|string $entity
     * @return bool
     */
    public function supportsEntity($entity);

    /**
     * @param LibraryAdTagInterface $libraryAdTag
     * @return void
     */
    public function save(LibraryAdTagInterface $libraryAdTag);

    /**
     * @param LibraryAdTagInterface $libraryAdTag
     * @return void
     */
    public function delete(LibraryAdTagInterface $libraryAdTag);

    /**
     * @return LibraryAdTagInterface
     */
    public function createNew();

    /**
     * @param int $id
     * @return LibraryAdTagInterface|null
     */
    public function find($id);

    /**
     * @param int|null $limit
     * @param int|null $offset
     * @return LibraryAdTagInterface[]
     */
    public function all($limit = null, $offset = null);


    /**
     * @param PublisherInterface $publisher
     * @param null $limit
     * @param null $offset
     * @return mixed
     */
    public function getLibraryAdTagsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);

}