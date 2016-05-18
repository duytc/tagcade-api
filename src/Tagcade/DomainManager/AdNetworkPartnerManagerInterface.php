<?php

namespace Tagcade\DomainManager;

use Tagcade\Model\Core\AdNetworkPartnerInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface AdNetworkPartnerManagerInterface extends ManagerInterface
{
    /**
     * @param PublisherInterface $publisher
     * @param int|null $limit
     * @param int|null $offset
     * @return AdNetworkPartnerInterface[]
     */
    public function getAdNetworkPartnersForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);

    /**
     * @param $name
     * @return AdNetworkPartnerInterface|null
     */
    public function getByCanonicalName($name);

}