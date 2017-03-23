<?php


namespace Tagcade\Cache\V2;


use Tagcade\Model\Core\DisplayBlacklistInterface;

interface DisplayDomainListManagerInterface
{

    /**
     * @param DisplayBlacklistInterface $blacklist
     * @return mixed
     */
    public function saveBlacklist(DisplayBlacklistInterface $blacklist);

    /**
     * @param DisplayBlacklistInterface $blacklist
     * @return mixed
     */
    public function delBlacklist(DisplayBlacklistInterface $blacklist);

    /**
     * @param $displayBlacklistId
     * @return array
     */
    public function getDomainsForBlacklist($displayBlacklistId);
}