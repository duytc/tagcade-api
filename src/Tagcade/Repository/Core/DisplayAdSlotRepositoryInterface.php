<?php

namespace Tagcade\Repository\Core;

use Doctrine\Common\Persistence\ObjectRepository;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\PagerParam;
use Tagcade\Model\User\Role\UserRoleInterface;

interface DisplayAdSlotRepositoryInterface extends ObjectRepository
{
    /**
     * @param SiteInterface $site
     * @param $name
     * @return mixed
     */
    public function getAdSlotForSiteByName(SiteInterface $site, $name);

    /**
     * @param SiteInterface $site
     * @return mixed
     */
    public function deleteAdSlotForSite(SiteInterface $site);

    /**
     * @param $libraryDisplayAdSlotId
     * @return mixed
     */
    public function getSitesByLibraryDisplayAdSlot($libraryDisplayAdSlotId);

    /**
     * @param UserRoleInterface $user
     * @param PagerParam $param
     * @return mixed
     */
    public function getAdSlotsForUserWithPagination(UserRoleInterface $user, PagerParam $param = null);
}