<?php

namespace Tagcade\DomainManager;

use Tagcade\Model\Core\ExchangeInterface;

interface ExchangeManagerInterface extends ManagerInterface
{
    /**
     * @param $name
     * @return ExchangeInterface|null
     */
    public function getExchangeByName($name);

    /**
     * @param $name
     * @return ExchangeInterface|null
     */
    public function getExchangeByCanonicalName($name);
}