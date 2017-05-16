<?php

namespace Tagcade\Entity\Core;
use \Tagcade\Model\Core\NetworkWhiteList as NetworkWhiteListModel;
class NetworkWhiteList extends NetworkWhiteListModel
{
    protected $id;
    protected $adNetwork;
    protected $publisher;
    protected $displayWhiteList;
}