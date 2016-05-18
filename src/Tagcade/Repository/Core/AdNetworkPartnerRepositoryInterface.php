<?php

namespace Tagcade\Repository\Core;

use Doctrine\Common\Persistence\ObjectRepository;
use Tagcade\Model\Core\AdNetworkPartnerInterface;
use Tagcade\Model\User\Role\UserRoleInterface;

interface AdNetworkPartnerRepositoryInterface extends ObjectRepository
{
    /**
     * find By Publisher
     *
     * @param int $publisherId
     * @return AdNetworkPartnerInterface[]
     */
    public function findByPublisher($publisherId);

    /**
     * find By UserRole
     *
     * @param UserRoleInterface $user
     * @return mixed
     */
    public function findByUserRole(UserRoleInterface $user);

    /**
     * find Unused Partners For Publisher
     *
     * @param UserRoleInterface $publisher
     * @return mixed
     */
    public function findUnusedPartnersForPublisher(UserRoleInterface $publisher);

    /**
     * find By CanonicalName
     *
     * @param $name
     * @return mixed
     */
    public function findByCanonicalName($name);
}