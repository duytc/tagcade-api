<?php

namespace Tagcade\Entity\Core;

use Tagcade\Model\Core\DisplayBlacklist as DisplayBlacklistModel;

class DisplayBlacklist extends DisplayBlacklistModel
{
    protected $id;
    protected $name;
    protected $domains;
    protected $networkBlacklists;
    protected $publisher;
    protected $blacklistExpressions;

    /**
     * @var bool
     */
    protected $isDefault;
}