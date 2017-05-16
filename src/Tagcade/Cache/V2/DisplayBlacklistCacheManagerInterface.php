<?php


namespace Tagcade\Cache\V2;


use Redis;
use Tagcade\Model\Core\DisplayBlacklistInterface;

interface DisplayBlacklistCacheManagerInterface
{

    /**
     * @param DisplayBlacklistInterface $blacklist
     * @param array $refinedDomains
     * @return mixed
     */
    public function saveBlacklist(DisplayBlacklistInterface $blacklist, array $refinedDomains = null);

    /**
     * @param DisplayBlacklistInterface $blacklist
     * @return mixed
     */
    public function deleteBlacklist(DisplayBlacklistInterface $blacklist);

    /**
     * @param $displayBlacklistId
     * @return array
     */
    public function getDomainsForBlacklist($displayBlacklistId);

    /**
     * @return Redis
     */
    public function getRedis();
}