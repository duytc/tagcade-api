<?php

namespace Tagcade\Repository\Core;

use Doctrine\Common\Persistence\ObjectRepository;
use Tagcade\Model\PagerParam;
use Tagcade\Model\User\Role\UserRoleInterface;

interface NativeAdSlotRepositoryInterface extends ObjectRepository
{
    /**
     * @param UserRoleInterface $user
     * @param PagerParam $param
     * @return mixed
     */
    public function getAdSlotsForUserWithPagination(UserRoleInterface $user, PagerParam $param = null);
}