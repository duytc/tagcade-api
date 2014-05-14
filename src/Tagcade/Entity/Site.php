<?php

namespace Tagcade\Entity;

use Tagcade\Model\Site as SiteModel;
use Tagcade\Bundle\UserBundle\Entity\User;

class Site extends SiteModel
{
    protected $id;

    /**
     * @var User
     */
    protected $publisher;
    protected $name;
    protected $domain;
}