<?php

namespace Tagcade\Entity\Core;

use Tagcade\Model\Core\WhiteList as WhiteListModel;

class WhiteList extends WhiteListModel
{
    protected $id;
    protected $name;
    protected $suffixKey;
    protected $domains;
    protected $publisher;
}