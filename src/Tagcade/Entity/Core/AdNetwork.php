<?php

namespace Tagcade\Entity\Core;

use Tagcade\Model\Core\AdNetwork as AdNetworkModel;

class AdNetwork extends AdNetworkModel
{
    protected $id;
    protected $publisher;
    protected $name;
    protected $url;
    protected $active;
    protected $cpmRate;

    public function __construct()
    {}
}