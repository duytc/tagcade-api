<?php

namespace Tagcade\Repository\Core;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\QueryBuilder;
use Tagcade\Model\Core\AdNetworkPartnerInterface;
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

    public function getAdNetworksThatHavePartnerForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);

    public function getAdNetworksThatHavePartnerForSubPublisher(SubPublisherInterface $publisher, $limit = null, $offset = null);

    public function getAdNetworksForPublisherAndPartner(PublisherInterface $publisher, AdNetworkPartnerInterface $partner, $limit = null, $offset = null);

    public function allHasCap($limit = null, $offset = null);


    /**
     * @param PublisherInterface|SubPublisherInterface $publisher
     * @param int|null $limit
     * @param int|null $offset
     * @return QueryBuilder
     */
    public function getAdNetworksForPublisherQuery(PublisherInterface $publisher, $limit = null, $offset = null);

    public function getPartnerConfigurationForAllPublishers($partnerCName, $publisherId, $withUnifiedReportModuleEnabled = true);

    /**
     * @param $publisher
     * @param $partnerCName
     * @return mixed
     */
    public function getAdNetworkByPublisherAndPartnerCName($publisher, $partnerCName);

    /**
     * @param $publisherId
     * @param $partnerCName
     * @param $emailToken
     * @return mixed
     */
    public function validateEmailToken($publisherId, $partnerCName, $emailToken);

    /**
     * @param UserRoleInterface $user
     * @param PagerParam $param
     * @param null $builtIn
     * @return mixed
     */
    public function getAdNetworksForUserWithPagination(UserRoleInterface $user, PagerParam $param, $builtIn = null);
}