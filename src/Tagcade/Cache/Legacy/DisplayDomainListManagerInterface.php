<?php


namespace Tagcade\Cache\Legacy;


use Tagcade\Model\Core\DisplayBlacklistInterface;

interface DisplayDomainListManagerInterface
{

    /**
     * @param DisplayBlacklistInterface $blacklist
     * @return mixed
     */
    public function saveBlacklist(DisplayBlacklistInterface $blacklist);

    /**
     * @param $suffixKey
     * @return array
     */
    public function getDomainsForBlacklist($suffixKey);
}