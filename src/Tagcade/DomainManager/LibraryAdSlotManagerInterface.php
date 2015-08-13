<?php

namespace Tagcade\DomainManager;

use Tagcade\Model\Core\BaseLibraryAdSlotInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface LibraryAdSlotManagerInterface extends ManagerInterface
{
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