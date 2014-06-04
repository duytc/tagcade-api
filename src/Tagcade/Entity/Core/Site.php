<?php

namespace Tagcade\Entity\Core;

use Tagcade\Model\Site as SiteModel;

class Site extends SiteModel
{
    protected $id;
    protected $publisher;
    protected $name;
    protected $domain;

    public function __construct()
    {}
}