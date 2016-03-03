<?php

namespace Tagcade\Entity\Core;

use Tagcade\Model\Core\Exchange as ExchangeModel;

class Exchange extends ExchangeModel
{
    protected $id;
    protected $name;
    protected $canonicalName;
    protected $publisherExchanges;

    public function __construct()
    {
    }
}