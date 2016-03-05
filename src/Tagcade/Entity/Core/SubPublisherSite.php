<?php

namespace Tagcade\Entity\Core;

use Tagcade\Model\Core\SubPublisherSite as SubPublisherSiteModel;

class SubPublisherSite extends SubPublisherSiteModel
{
    protected $id;
    protected $subPublisher;
    protected $site;
    protected $access;
    protected $deletedAt;
}