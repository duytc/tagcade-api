<?php

namespace Tagcade\Entity\Core;

use Tagcade\Model\Core\DisplayWhiteList as DisplayWhiteListModel;

class DisplayWhiteList extends DisplayWhiteListModel
{
    protected $id;
    protected $name;
    protected $domains;
    protected $networkWhiteLists;
    protected $publisher;
}