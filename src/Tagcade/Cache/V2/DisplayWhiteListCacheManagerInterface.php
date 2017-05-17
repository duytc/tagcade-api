<?php


namespace Tagcade\Cache\V2;


use Tagcade\Model\Core\DisplayWhiteListInterface;

interface DisplayWhiteListCacheManagerInterface
{

    /**
     * @param DisplayWhiteListInterface $whiteList
     * @param array $refinedDomains
     * @return mixed
     */
    public function saveWhiteList(DisplayWhiteListInterface $whiteList, array $refinedDomains = null);

    /**
     * @param DisplayWhiteListInterface $whiteList
     * @return mixed
     */
    public function deleteWhiteList(DisplayWhiteListInterface $whiteList);

    /**
     * @param $displayWhiteListId
     * @return array
     */
    public function getDomainsForWhiteList($displayWhiteListId);
}