<?php


namespace Tagcade\Service\Core\Exchange;


interface ExchangeCacheUpdaterInterface
{
    /**
     * @param $oldName
     * @param $newName
     * @return mixed
     */
    public function updateCacheAfterUpdateExchangeParameter($oldName, $newName = null);
}