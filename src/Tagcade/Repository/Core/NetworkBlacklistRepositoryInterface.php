<?php

namespace Tagcade\Repository\Core;

use Doctrine\Common\Persistence\ObjectRepository;
use Tagcade\Model\Core\DisplayBlacklistInterface;

interface NetworkBlacklistRepositoryInterface extends ObjectRepository
{
    /**
     * @param DisplayBlacklistInterface $displayBlacklist
     * @return mixed
     */
    public function getAdNetworksForDisplayBlacklist(DisplayBlacklistInterface $displayBlacklist);

    /**
     * @param DisplayBlacklistInterface $displayBlacklist
     * @return mixed
     */
    public function getDefaultNetworkForDisplayBlacklist(DisplayBlacklistInterface $displayBlacklist);
}