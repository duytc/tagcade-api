<?php

namespace Tagcade\Entity;

use Tagcade\Model\Site as SiteModel;

class Site extends SiteModel
{
    protected $id;
    protected $publisher;
    protected $name;
    protected $domain;
}