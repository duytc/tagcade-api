<?php

namespace Tagcade\Entity\Core;
use \Tagcade\Model\Core\NetworkBlacklist as NetworkBlacklistModel;
class NetworkBlacklist extends NetworkBlacklistModel
{
    protected $id;
    protected $adNetwork;
    protected $publisher;
    protected $displayBlacklist;
}