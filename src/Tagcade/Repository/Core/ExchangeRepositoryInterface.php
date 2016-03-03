<?php

namespace Tagcade\Repository\Core;

use Doctrine\Common\Persistence\ObjectRepository;
use Tagcade\Model\Core\ExchangeInterface;

interface ExchangeRepositoryInterface extends ObjectRepository
{
    /**
     * @param $name
     * @return ExchangeInterface|null
     */
    public function getExchangeByName($name);

    /**
     * @param $name
     * @return mixed
     */
    public function getExchangeByCanonicalName($name);
}