<?php


namespace Tagcade\Service\Core\DisplayBlacklist;


use Tagcade\Cache\V2\DisplayBlacklistCacheManagerInterface;
use Tagcade\DomainManager\DisplayBlacklistManagerInterface;
use Tagcade\Model\Core\DisplayBlacklistInterface;

class RefreshBlacklistCacheService implements RefreshBlacklistCacheServiceInterface
{
    /**
     * @var DisplayBlacklistCacheManagerInterface
     */
    protected $displayBlacklistCacheManager;

    /**
     * @var DisplayBlacklistManagerInterface
     */
    protected $displayBlacklistManager;

    /**
     * RefreshBlacklistCacheService constructor.
     * @param DisplayBlacklistCacheManagerInterface $displayBlacklistCacheManager
     * @param DisplayBlacklistManagerInterface $displayBlacklistManager
     */
    public function __construct(DisplayBlacklistCacheManagerInterface $displayBlacklistCacheManager, DisplayBlacklistManagerInterface $displayBlacklistManager)
    {
        $this->displayBlacklistCacheManager = $displayBlacklistCacheManager;
        $this->displayBlacklistManager = $displayBlacklistManager;
    }


    public function refreshCacheForSingleBlacklist(DisplayBlacklistInterface $blacklist)
    {
        $domains = $blacklist->getDomains();
        $domains = array_map(function($domain) {
            $domain = strtolower($domain);
            return preg_replace('/^www./', '', $domain);
        }, $domains);

        $domains = array_values(array_unique($domains));
        $this->displayBlacklistCacheManager->saveBlacklist($blacklist, $domains);
        $blacklist->setDomains($domains);
        $this->displayBlacklistManager->save($blacklist);
    }

    public function refreshCacheForAllBlacklist()
    {
        $blacklists = $this->displayBlacklistManager->all();
        foreach ($blacklists as $blacklist) {
            $this->refreshCacheForSingleBlacklist($blacklist);
        }
    }

    public function removeCacheKeyForSingleBlacklist(DisplayBlacklistInterface $blacklist)
    {
        $this->displayBlacklistCacheManager->deleteBlacklist($blacklist);
    }
}