<?php

namespace Tagcade\Repository\Core;

use Doctrine\Common\Persistence\ObjectRepository;
use Tagcade\Model\Core\AdNetworkPartnerInterface;

interface AdNetworkPartnerRepositoryInterface extends ObjectRepository
{
    /**
     * @param int $publisherId
     * @return AdNetworkPartnerInterface[]
     */
    public function findByPublisher($publisherId);
}