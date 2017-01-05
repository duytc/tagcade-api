<?php

namespace Tagcade\Repository\Core;

use Doctrine\Common\Persistence\ObjectRepository;
use Tagcade\Model\Core\DynamicAdSlotInterface;
use Tagcade\Model\Core\ReportableAdSlotInterface;
use Tagcade\Model\PagerParam;
use Tagcade\Model\User\Role\UserRoleInterface;

interface DynamicAdSlotRepositoryInterface extends ObjectRepository
{
    /**
     * @param ReportableAdSlotInterface $adSlot
     * @param null $limit
     * @param null $offset
     * @return DynamicAdSlotInterface[]
     */
    public function getDynamicAdSlotsThatHaveDefaultAdSlot(ReportableAdSlotInterface $adSlot, $limit = null, $offset = null);

    /**
     * @param UserRoleInterface $user
     * @param PagerParam $param
     * @return mixed
     */
    public function getAdSlotsForUserWithPagination(UserRoleInterface $user, PagerParam $param = null);
}