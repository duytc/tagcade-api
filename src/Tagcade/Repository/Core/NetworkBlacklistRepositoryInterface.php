<?php

namespace Tagcade\Repository\Core;

use Doctrine\Common\Persistence\ObjectRepository;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\DisplayBlacklistInterface;

interface NetworkBlacklistRepositoryInterface extends ObjectRepository
{
    /**
     * @param DisplayBlacklistInterface $displayBlacklist
     * @return mixed
     */
    public function getForDisplayBlacklist(DisplayBlacklistInterface $displayBlacklist);

    /**
     * @param AdNetworkInterface $adNetwork
     * @return mixed
     */
    public function getForAdNetwork(AdNetworkInterface $adNetwork);
}