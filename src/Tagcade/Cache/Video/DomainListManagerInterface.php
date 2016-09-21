<?php


namespace Tagcade\Cache\Video;


use Tagcade\Model\Core\BlacklistInterface;
use Tagcade\Model\Core\WhiteListInterface;

interface DomainListManagerInterface
{

    /**
     * @param BlacklistInterface $blacklist
     * @return mixed
     */
    public function saveBlacklist(BlacklistInterface $blacklist);

    /**
     * @param WhiteListInterface $whiteList
     * @return mixed
     */
    public function saveWhiteList(WhiteListInterface $whiteList);

    /**
     * @param $suffixKey
     * @return array
     */
    public function getDomainsForBlacklist($suffixKey);
}