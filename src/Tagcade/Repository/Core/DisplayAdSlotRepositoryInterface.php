<?php

namespace Tagcade\Repository\Core;

use Doctrine\Common\Persistence\ObjectRepository;
use Tagcade\Model\Core\SiteInterface;

interface DisplayAdSlotRepositoryInterface extends ObjectRepository
{
    public function getAdSlotForSiteByName(SiteInterface $site, $name);

    public function deleteAdSlotForSite(SiteInterface $site);

    public function getSitesByLibraryDisplayAdSlot($libraryDisplayAdSlotId);
}