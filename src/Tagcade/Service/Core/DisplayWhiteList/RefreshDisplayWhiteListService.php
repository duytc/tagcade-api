<?php


namespace Tagcade\Service\Core\DisplayWhiteList;


use Tagcade\Cache\V2\DisplayWhiteListCacheManagerInterface;
use Tagcade\DomainManager\DisplayWhiteListManagerInterface;
use Tagcade\Model\Core\DisplayWhiteListInterface;

class RefreshDisplayWhiteListService implements RefreshDisplayWhiteListServiceInterface
{
    /**
     * @var DisplayWhiteListCacheManagerInterface
     */
    protected $displayWhiteListCacheManager;

    /**
     * @var DisplayWhiteListManagerInterface
     */
    protected $displayWhiteListManager;

    /**
     * RefreshDisplayWhiteListService constructor.
     * @param DisplayWhiteListCacheManagerInterface $displayWhiteListCacheManager
     * @param DisplayWhiteListManagerInterface $displayWhiteListManager
     */
    public function __construct(DisplayWhiteListCacheManagerInterface $displayWhiteListCacheManager, DisplayWhiteListManagerInterface $displayWhiteListManager)
    {
        $this->displayWhiteListCacheManager = $displayWhiteListCacheManager;
        $this->displayWhiteListManager = $displayWhiteListManager;
    }

    /**
     * @param DisplayWhiteListInterface $whiteList
     */
    public function refreshCacheForASingleWhiteList(DisplayWhiteListInterface $whiteList)
    {
        $domains = $whiteList->getDomains();
        $domains = array_map(function($domain) {
            $domain = strtolower($domain);
            return preg_replace('/^www./', '', $domain);
        }, $domains);

        $domains = array_values(array_unique($domains));
        $this->displayWhiteListCacheManager->saveWhiteList($whiteList, $domains);
        $whiteList->setDomains($domains);
        $this->displayWhiteListManager->save($whiteList);
    }

    public function refreshCacheForAllWhiteList()
    {
        $whiteLists = $this->displayWhiteListManager->all();
        foreach ($whiteLists as $whiteList) {
            $this->refreshCacheForASingleWhiteList($whiteList);
        }
    }
}