<?php

namespace Tagcade\DomainManager;

use Tagcade\Model\Core\BaseLibraryAdSlotInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface LibraryAdSlotManagerInterface
{
    /**
     * @see \Tagcade\DomainManager\ManagerInterface
     *
     * @param BaseLibraryAdSlotInterface|string $entity
     * @return bool
     */
    public function supportsEntity($entity);

    /**
     * @param BaseLibraryAdSlotInterface $libraryAdSlot
     * @return void
     */
    public function save(BaseLibraryAdSlotInterface $libraryAdSlot);

    /**
     * @param BaseLibraryAdSlotInterface $libraryAdSlot
     * @return void
     */
    public function delete(BaseLibraryAdSlotInterface $libraryAdSlot);

    /**
     * @return BaseLibraryAdSlotInterface
     */
    public function createNew();

    /**
     * @param int $id
     * @return BaseLibraryAdSlotInterface|null
     */
    public function find($id);

    /**
     * @param int|null $limit
     * @param int|null $offset
     * @return BaseLibraryAdSlotInterface[]
     */
    public function all($limit = null, $offset = null);


    /**
     * @param PublisherInterface $publisher
     * @param int|null $limit
     * @param int|null $offset
     * @return BaseLibraryAdSlotInterface[]
     */
    public function getLibraryDisplayAdSlotsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);


    /**
     * Get those library ad slots that haven't been referred by any ad slot
     *
     * @param SiteInterface $site
     * @param null $limit
     * @param null $offset
     * @return mixed
     */
    public function getUnReferencedLibraryAdSlotForSite(SiteInterface $site, $limit = null, $offset = null);
}