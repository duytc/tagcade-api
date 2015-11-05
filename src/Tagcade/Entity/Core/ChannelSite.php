<?php

namespace Tagcade\Entity\Core;

use Tagcade\Model\Core\ChannelSite as ChannelSiteModel;

class ChannelSite extends ChannelSiteModel
{
    protected $id;
    protected $channel;
    protected $site;
    protected $deletedAt;

    public function __construct()
    {
    }
}