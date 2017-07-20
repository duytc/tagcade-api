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

    /**
     * this constructor will be called by FormType, must be used to call parent to set default values
     */
    public function __construct()
    {
        parent::__construct();
    }
}