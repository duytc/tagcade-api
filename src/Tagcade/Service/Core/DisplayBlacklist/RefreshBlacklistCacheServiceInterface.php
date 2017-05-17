<?php


namespace Tagcade\Service\Core\DisplayBlacklist;


use Tagcade\Model\Core\DisplayBlacklistInterface;

interface RefreshBlacklistCacheServiceInterface
{
    /**
     * @param DisplayBlacklistInterface $blacklist
     * @return mixed
     */
    public function refreshCacheForSingleBlacklist(DisplayBlacklistInterface $blacklist);

    /**
     * @return mixed
     */
    public function refreshCacheForAllBlacklist();

    /**
     * @param DisplayBlacklistInterface $blacklist
     * @return mixed
     */
    public function removeCacheKeyForSingleBlacklist(DisplayBlacklistInterface $blacklist);
}