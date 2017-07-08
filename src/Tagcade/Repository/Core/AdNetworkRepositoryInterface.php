<?php

namespace Tagcade\Repository\Core;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\QueryBuilder;
use Tagcade\Model\Core\DisplayBlacklistInterface;
use Tagcade\Model\PagerParam;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;
use Tagcade\Model\User\Role\UserRoleInterface;

interface AdNetworkRepositoryInterface extends ObjectRepository
{
    /**
     * @param PublisherInterface $publisher
     * @param int|null $limit
     * @param int|null $offset
     * @return array
     */
    public function getAdNetworksForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);

    public function getAdNetworksForActivePublishers();

    public function getAdNetworksForDisplayBlacklist(DisplayBlacklistInterface $displayBlacklist, $limit = null, $offset = null);

    public function allHasCap($limit = null, $offset = null);


    /**
     * @param PublisherInterface|SubPublisherInterface $publisher
     * @param int|null $limit
     * @param int|null $offset
     * @return QueryBuilder
     */
    public function getAdNetworksForPublisherQuery(PublisherInterface $publisher, $limit = null, $offset = null);

    /**
     * @param UserRoleInterface $user
     * @param PagerParam $param
     * @return mixed
     */
    public function getAdNetworksForUserWithPagination(UserRoleInterface $user, PagerParam $param);
}