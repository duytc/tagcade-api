<?php

namespace Tagcade\Entity\Core;

use Tagcade\Model\Core\Channel as ChannelModel;

class Channel extends ChannelModel
{
    protected $id;
    protected $publisher;
    protected $name;
    protected $deletedAt;
    protected $channelSites;

    public function __construct()
    {
    }
}