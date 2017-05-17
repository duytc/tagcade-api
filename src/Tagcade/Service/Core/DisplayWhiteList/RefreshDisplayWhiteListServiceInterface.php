<?php


namespace Tagcade\Service\Core\DisplayWhiteList;


use Tagcade\Model\Core\DisplayWhiteListInterface;

interface RefreshDisplayWhiteListServiceInterface
{
    /**
     * @param DisplayWhiteListInterface $whiteList
     * @return mixed
     */
    public function refreshCacheForASingleWhiteList(DisplayWhiteListInterface $whiteList);

    /**
     * @return mixed
     */
    public function refreshCacheForAllWhiteList();
}