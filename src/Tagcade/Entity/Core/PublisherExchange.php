<?php

namespace Tagcade\Entity\Core;

use Tagcade\Model\Core\PublisherExchange as PublisherExchangeModel;

class PublisherExchange extends PublisherExchangeModel
{
    protected $id;
    protected $publisher;
    protected $exchange;

    public function __construct()
    {
    }
}