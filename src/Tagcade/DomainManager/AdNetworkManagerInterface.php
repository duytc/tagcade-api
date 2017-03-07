<?php

namespace Tagcade\DomainManager;

use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;

interface AdNetworkManagerInterface extends ManagerInterface
{
    /**
     * @param PublisherInterface $publisher
     * @param int|null $limit
     * @param int|null $offset
     * @return AdNetworkInterface[]
     */
    public function getAdNetworksForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);

    public function getAdNetworksThatHavePartnerForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);

    public function getAdNetworksThatHavePartnerForSubPublisher(SubPublisherInterface $publisher, $limit = null, $offset = null);

    /**
     * @return AdNetworkInterface[]
     */
    public function allActiveAdNetworks();

    /**
     * @inheritdoc
     */
    public function allHasCap($limit = null, $offset = null);

    /**
     * @param $publisherId
     * @param $partnerCName
     * @param $token
     * @return mixed
     */
    public function validateEmailHookToken($publisherId, $partnerCName, $token);

    /**
     * @param bool $resetToken
     * @param AdNetworkInterface $adNetwork
     * @return mixed
     */
    public function getUnifiedReportEmail(AdNetworkInterface $adNetwork, $resetToken = false);
}